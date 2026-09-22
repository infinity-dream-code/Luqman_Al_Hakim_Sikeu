(function () {
    const keepAliveUrl = document.querySelector('meta[name="keep-alive-url"]')?.getAttribute('content');
    if (!keepAliveUrl) {
        return;
    }

    let keepAliveInFlight = null;
    const nativeFetch = window.fetch.bind(window);

    function currentToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function applyToken(token) {
        if (!token) {
            return;
        }

        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.setAttribute('content', token);
        }

        document.querySelectorAll('input[name="_token"]').forEach(function (input) {
            input.value = token;
        });

        if (window.jQuery) {
            window.jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
        }
    }

    function isSessionNoise(message) {
        const text = String(message || '')
            .replace(/<[^>]*>/g, ' ')
            .toLowerCase();

        return text.includes('unauthenticated')
            || text.includes('sesi anda')
            || text.includes('sesi sudah')
            || text.includes('sesi berakhir')
            || text.includes('session expired')
            || text.includes('login kembali')
            || text.includes('silahkan login')
            || text.includes('silakan login');
    }

    function wrapErrorAlert() {
        if (typeof window.errorAlert !== 'function' || window.errorAlert.__sessionKeepAliveWrapped) {
            return;
        }

        const original = window.errorAlert;
        window.errorAlert = function (message) {
            if (isSessionNoise(message)) {
                keepAlive();
                return;
            }
            return original.apply(this, arguments);
        };
        window.errorAlert.__sessionKeepAliveWrapped = true;
    }

    function keepAlive() {
        if (keepAliveInFlight) {
            return keepAliveInFlight;
        }

        const url = keepAliveUrl + (keepAliveUrl.indexOf('?') === -1 ? '?' : '&') + '_ts=' + Date.now();

        keepAliveInFlight = nativeFetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        }).then(function (res) {
            if (!res.ok) {
                return null;
            }
            return res.json();
        }).then(function (data) {
            if (data && data.token) {
                applyToken(data.token);
                return data.token;
            }
            return null;
        }).catch(function () {
            return null;
        }).finally(function () {
            keepAliveInFlight = null;
        });

        return keepAliveInFlight;
    }

    function withCsrfHeaders(input, init) {
        const next = Object.assign({}, init || {});
        next.credentials = next.credentials || 'same-origin';

        const headers = new Headers();
        if (typeof Request !== 'undefined' && input instanceof Request) {
            input.headers.forEach(function (value, key) {
                headers.set(key, value);
            });
        }
        if (next.headers) {
            new Headers(next.headers).forEach(function (value, key) {
                headers.set(key, value);
            });
        }

        const token = currentToken();
        if (token && !headers.has('X-CSRF-TOKEN') && !headers.has('X-XSRF-TOKEN')) {
            headers.set('X-CSRF-TOKEN', token);
        }

        next.headers = headers;
        return next;
    }

    function retryInit(input, init, token) {
        const next = withCsrfHeaders(input, init);
        if (token) {
            next.headers.set('X-CSRF-TOKEN', token);
        }

        if (next.body instanceof FormData && token) {
            next.body.set('_token', token);
        }

        return next;
    }

    function shouldRetryStatus(status) {
        return status === 419 || status === 401;
    }

    function isKeepAliveUrl(input) {
        const url = typeof input === 'string' ? input : (input && input.url ? input.url : '');
        return typeof url === 'string' && url.indexOf('/keep-alive') !== -1;
    }

    window.fetch = function (input, init) {
        if (isKeepAliveUrl(input)) {
            return nativeFetch(input, init);
        }

        const canClone = typeof Request !== 'undefined' && input instanceof Request;
        const firstInput = canClone ? input.clone() : input;
        const firstInit = withCsrfHeaders(input, init);

        return nativeFetch(firstInput, firstInit).then(function (res) {
            if (!shouldRetryStatus(res.status)) {
                return res;
            }

            return keepAlive().then(function (token) {
                const secondInput = canClone ? input.clone() : input;
                return nativeFetch(secondInput, retryInit(input, init, token || currentToken()));
            });
        });
    };

    function patchJQueryAjax($) {
        if (!$ || $.ajax.__sessionKeepAlivePatched) {
            return;
        }

        const originalAjax = $.ajax.bind($);

        $.ajax = function (url, settings) {
            if (settings === undefined && typeof url === 'object') {
                settings = url;
            } else {
                settings = settings || {};
                if (url !== undefined) {
                    settings.url = url;
                }
            }

            settings = $.extend(true, {}, settings);

            if (settings._sessionKeepAliveRetried) {
                return originalAjax(settings);
            }

            const userError = settings.error;
            const userSuccess = settings.success;
            const userComplete = settings.complete;
            const userStatusCode = settings.statusCode;
            delete settings.error;
            delete settings.success;
            delete settings.complete;
            delete settings.statusCode;

            const dfd = $.Deferred();
            const first = originalAjax(settings);

            function succeed(ctx, args) {
                if (typeof userSuccess === 'function') {
                    userSuccess.apply(ctx, args);
                }
                dfd.resolveWith(ctx, args);
            }

            function fail(ctx, args) {
                if (typeof userError === 'function') {
                    userError.apply(ctx, args);
                }
                dfd.rejectWith(ctx, args);
            }

            first.done(function () {
                succeed(this, arguments);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (!jqXHR || !shouldRetryStatus(jqXHR.status)) {
                    fail(this, arguments);
                    return;
                }

                const ctx = this;
                keepAlive().then(function (token) {
                    const retrySettings = $.extend(true, {}, settings);
                    retrySettings._sessionKeepAliveRetried = true;
                    retrySettings.headers = $.extend({}, retrySettings.headers, {
                        'X-CSRF-TOKEN': token || currentToken()
                    });

                    originalAjax(retrySettings).done(function () {
                        succeed(this, arguments);
                    }).fail(function () {
                        fail(this, arguments);
                    });
                }.bind(ctx));
            });

            if (typeof userComplete === 'function') {
                dfd.always(function () {
                    userComplete.apply(this, arguments);
                });
            }

            return dfd.promise(first);
        };

        $.ajax.__sessionKeepAlivePatched = true;
    }

    wrapErrorAlert();
    if (window.jQuery) {
        patchJQueryAjax(window.jQuery);
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            wrapErrorAlert();
            if (window.jQuery) {
                patchJQueryAjax(window.jQuery);
            }
        });
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            keepAlive();
        }
    });

    window.addEventListener('focus', function () {
        keepAlive();
    });

    setInterval(keepAlive, 2 * 60 * 1000);
    keepAlive();
})();
