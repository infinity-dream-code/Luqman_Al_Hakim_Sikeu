@extends('layouts.login_new')
@section('title', 'Login Approval Prestasi')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
                <div>
                    <img src="{{ asset('hakim.jpeg') }}" alt="logo" style="width: 120px; height: 120px;" class="mb-5">
                    <h3 class="mb-2">Approval Prestasi</h3>
                    <p class="text-muted">Login admin untuk approve/tolak prestasi siswa.</p>
                </div>
            </div>

            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Masuk Approval Prestasi</h4>
                    <p class="mb-5">Gunakan akun `user_prestasi` (non-siswa).</p>

                    <form method="POST" action="{{ route('approval-prestasi.login') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required autofocus value="{{ old('username') }}">
                        </div>
                        <div class="mb-5">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger py-2">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary d-grid w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

