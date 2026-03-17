{{-- Note --}}
@extends('layouts.admin.guest')

@section('content')
<div class="auth-minimal-inner">
    <div class="minimal-card-wrapper">
        <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
            <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                <img src="{{ asset('assets/admin/images/logo-abbr.png') }}" alt="" class="img-fluid">
            </div>
            <div class="card-body p-sm-5">
                <h2 class="fs-20 fw-bolder mb-4">Login</h2>
                <h4 class="fs-13 fw-bold mb-2">Login to your account</h4>
                <p class="fs-12 fw-medium text-muted">Thank you for get back <strong>Nelel</strong> web applications, let's access our the best recommendation for you.</p>
                <form action="{{ route('admin.login.submit') }}" method="POST" class="w-100 mt-4 pt-2">
                    @csrf
                    <x-admin.form.text-input
                        name="email"
                        type="email"
                        label="Email or Username"
                        placeholder="Email or Username"
                        required
                        autocomplete="email"
                    />

                    <x-admin.form.text-input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="Password"
                        required
                        autocomplete="current-password"
                        :revealable="true"
                    />
                    <div class="d-flex align-items-center justify-content-between">
                        <x-admin.form.checkbox
                            name="remember"
                            label="Remember Me"
                        />
                        {{-- <div>
                            <a href="auth-reset-minimal.html" class="fs-11 text-primary">Forget password?</a>
                        </div> --}}
                    </div>
                    <div class="mt-5">
                        <x-admin.ui.button
                            type="submit"
                            variant="primary"
                            class="btn-lg w-100"
                        >
                            Login
                        </x-admin.ui.button>
                    </div>
                </form>
                {{-- <div class="w-100 mt-5 text-center mx-auto">
                    <div class="mb-4 border-bottom position-relative"><span class="small py-1 px-3 text-uppercase text-muted bg-white position-absolute translate-middle">or</span></div>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Facebook">
                            <i class="feather-facebook"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Twitter">
                            <i class="feather-twitter"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-light-brand flex-fill" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Login with Github">
                            <i class="feather-github text"></i>
                        </a>
                    </div>
                </div> --}}
                {{-- <div class="mt-5 text-muted">
                    <span> Don't have an account?</span>
                    <a href="auth-register-minimal.html" class="fw-bold">Create an Account</a>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection