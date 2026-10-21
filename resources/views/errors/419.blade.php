@extends('errors::minimal')

@section('title', __('Memuat ulang'))
@section('code', '419')
@section('message', __('Memuat ulang halaman'))

@section('script')
    <script>
        (function () {
            const key = 'al_hakim_419_reloaded';
            if (!sessionStorage.getItem(key)) {
                sessionStorage.setItem(key, '1');
                window.location.reload();
                return;
            }
            sessionStorage.removeItem(key);
            const url = new URL(window.location.href);
            url.searchParams.set('_r', String(Date.now()));
            window.location.replace(url.toString());
        })();
    </script>
@endsection
