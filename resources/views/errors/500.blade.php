@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))

@section('script')
    <script>
        (function () {
            const key = 'at_tibyan_500_reloaded';
            if (!sessionStorage.getItem(key)) {
                sessionStorage.setItem(key, '1');
                window.location.reload();
                return;
            }
            sessionStorage.removeItem(key);
        })();
    </script>
@endsection
