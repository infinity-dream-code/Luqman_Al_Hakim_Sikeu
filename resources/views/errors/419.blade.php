@extends('errors::minimal')

@section('title', __('Memuat ulang'))
@section('code', '419')
@section('message', __('Memuat ulang halaman'))

@section('script')
    <script>
        (function () {
            const key = 'at_tibyan_419_reloaded';
            if (!sessionStorage.getItem(key)) {
                sessionStorage.setItem(key, '1');
                window.location.reload();
                return;
            }
            sessionStorage.removeItem(key);
            window.location.href = {{ Illuminate\Support\Js::from(url('/admin')) }};
        })();
    </script>
@endsection
