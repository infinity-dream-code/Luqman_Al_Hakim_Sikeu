@extends('layouts.login_layout')
@section('content')
    <link rel="stylesheet" href="{{asset('main/css/pages/page-auth.css')}}">
    <style>
        .invalid-feedback {
            display: block;
        }

    </style>

    <style>
        .multiple-bg {
            background: radial-gradient(circle,rgba(60, 199, 254, 1) 0%, rgba(158, 228, 255, 0.7) 30%, rgba(190, 238, 255, 0.55) 60%, rgba(209, 243, 255, 0.3) 90%);
        }
    </style>
    <div class="position-relative multiple-bg">
        <div class="authentication-wrapper authentication-basic container-p-y p-4">
            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card p-2">
                    <div class="app-brand justify-content-center mt-5">
                        <a href="{{route('index')}}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <span style="color: #666cff">
                                        <img width="100" height="100" src="{{asset('hakim.jpeg')}}" alt="logo">
                                </span>
                            </span>
                        </a>
                    </div>

                    <div class="card-body mt-2">
                        {{-- <div class="row text-center">
                            <h3>{{config('app.name')}}</h3>
                        </div> --}}
                        <div class="row">
                            <div class="col-8">
                                <h4 class="mb-2">Selamat Datang!</h4>
                                <p class="mb-4">Silahkan login terlebih dahulu</p>
                            </div>
                            <div class="col text-end">
                                <div class="dropdown-style-switcher dropdown me-1 me-xl-0">
                                    <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                       href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class='ri-22px'></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                                <span class="align-middle"><i class='ri-sun-line ri-22px me-3'></i>Terang</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                                <span class="align-middle"><i
                                                        class="ri-moon-clear-line ri-22px me-3"></i>Gelap</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                                <span class="align-middle"><i class="ri-computer-line ri-22px me-3"></i>Sistem</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if(config('app.demo_mode'))
                            <div class="row">
                                <div class="card shadow-none border my-5">
                                    <div class="card-body">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" placeholder="Akun Demo" autocomplete="off"
                                                       class="form-control" id="user_demo" name="user_demo"
                                                       readonly value="admin_demo"/>
                                                <label for="username">Username & Password akun demo</label>
                                            </div>
                                            <span class="input-group-text copy-demo"><i class="ri-file-copy-2-line"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <form id="formAuthentication" class="mb-3" action="{{route('login')}}" method="POST">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger py-2 mb-3" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ri-user-line"></i></span>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" placeholder="Masukkan Usermane Anda" id="username"
                                               name="username" autocomplete="off"
                                               class="form-control @error('username') is-invalid @enderror"
                                               autofocus
                                               required value="{{old('username')}}"/>
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                                @error('username')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ri-key-line"></i></span>
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" placeholder="Masukkan Password Anda" name="password"
                                               id="password"
                                               autocomplete="off"
                                               class="form-control @error('password')is-invalid @enderror" required/>
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer showPassword"
                                          data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click"
                                          data-bs-placement="bottom"
                                          title="Lihat Password">
                                            <i class="ri ri-eye-off-line"></i>
                                    </span>
                                </div>
                                @error('password')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            @if(($useMathFallback ?? false) === false && filled(config('services.turnstile.site_key')) && config('services.turnstile.enabled', true) && !in_array(request()->getHost(), ['localhost', '127.0.0.1'], true))
                            <div class="mb-3 text-center">
                                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="id"></div>
                                @error('turnstile')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <a href="{{ route('login', ['cf_fallback' => 1]) }}" class="small">Captcha tidak muncul? Pakai mode manual</a>
                                </div>
                            </div>
                            @endif
                            @if(($useMathFallback ?? false) === true)
                                <input type="hidden" name="cf_fallback" value="1">
                                <div class="mb-3">
                                    <div class="alert alert-warning py-2 mb-2">
                                        Mode non-Cloudflare aktif.
                                    <a href="{{ route('login', ['cf_fallback' => 0]) }}" class="ms-2">Coba mode Cloudflare</a>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ri-function-line"></i></span>
                                        <div class="form-floating form-floating-outline">
                                            <input
                                                type="number"
                                                placeholder="Isi jawaban hitung"
                                                id="math_answer"
                                                name="math_answer"
                                                autocomplete="off"
                                                class="form-control @error('math_answer') is-invalid @enderror"
                                                required
                                            />
                                            <label for="math_answer">Hitung: {{ $mathLeft ?? 0 }} {{ $mathOperator ?? '+' }} {{ $mathRight ?? 0 }}</label>
                                        </div>
                                    </div>
                                    @error('math_answer')
                                        <div class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            @endif
                            <div class="mb-3 d-flex justify-content-start">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember-me"/>
                                    <label class="form-check-label" for="remember-me"> ingat saya</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $showTurnstileWidget = ($useMathFallback ?? false) === false
            && filled(config('services.turnstile.site_key'))
            && config('services.turnstile.enabled', true)
            && !in_array(request()->getHost(), ['localhost', '127.0.0.1'], true);
    @endphp
    @if($showTurnstileWidget)
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer onerror="window.handleTurnstileLoadError && window.handleTurnstileLoadError()"></script>
    @endif

    <script>
        window.handleTurnstileLoadError = function () {
            @if(!$showTurnstileWidget)
            return;
            @endif

            const storageKey = 'cf_login_fail_count';
            const currentFail = Math.max(0, parseInt(localStorage.getItem(storageKey) || '0', 10));
            const nextFail = Math.max(0, currentFail + 1);
            localStorage.setItem(storageKey, String(nextFail));

            const target = new URL(window.location.href);
            target.searchParams.set('cf_fallback', '1');
            target.searchParams.set('cf_fail_count', String(nextFail));
            window.location.replace(target.toString());
        };

        document.addEventListener("DOMContentLoaded", function () {
            @if($errors->has('turnstile') && ($useMathFallback ?? false) === false)
            const target = new URL(window.location.href);
            target.searchParams.set('cf_fallback', '1');
            window.location.replace(target.toString());
            @endif

            $('#formAuthentication').on('submit', function () {
                loadingAlert('');
            });

            $('.showPassword').click(function () {
                const passInput = $('#password');
                const type = passInput.attr('type');
                const icon = $(this).children();
                const thisText = $(this);
                if (type === 'password') {
                    thisText.attr('title', 'Sembunyikan Password')
                    thisText.attr('data-bs-original-title', 'Sembunyikan Password')
                    passInput.attr('type', 'text')
                    icon.removeClass('ri ri-eye-off-line')
                    icon.addClass('ri ri-eye-line')
                } else {
                    thisText.attr('title', 'Lihat Password')
                    thisText.attr('data-bs-original-title', 'Lihat Password')
                    passInput.attr('type', 'password')
                    icon.removeClass('ri ri-eye-line')
                    icon.addClass('ri ri-eye-off-line')
                }
            })

            @if(config('app.demo_mode'))
            if (navigator.permissions) {
                navigator.permissions.query({name: "clipboard-write"}).then(permissionStatus => {
                    if (permissionStatus.state === "granted" || permissionStatus.state === "prompt") {
                        $('.copy-demo').click(function () {
                            let copyText = document.getElementById("user_demo");
                            if (copyText && (copyText.tagName === 'INPUT' || copyText.tagName === 'TEXTAREA')) {
                                copyText.select();
                                copyText.setSelectionRange(0, 99999);

                                navigator.clipboard.writeText(copyText.value).then(() => {
                                    alert("Copied the text: " + copyText.value);
                                }).catch(err => {
                                    console.error("Failed to copy: ", err);
                                });
                            } else {
                                console.error("Element with id 'user_demo' is not an input or textarea.");
                            }
                        })
                    } else {
                        $('.copy-demo').remove()
                    }
                });
            }
            @endif
        })

    </script>
@endsection
