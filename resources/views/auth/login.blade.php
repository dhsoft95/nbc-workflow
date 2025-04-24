@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="header">
        <div class="top" style="text-align: center;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Iconic">
            <p class="lead">Login to your account</p>
        </div>

    </div>
    <div class="body">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email" class="control-label sr-only">{{ __('Email') }}</label>
                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username">
                @error('email')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="password" class="control-label sr-only">{{ __('Password') }}</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required autocomplete="current-password">
                @error('password')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group clearfix">
                <label class="fancy-checkbox element-left">
                    <input type="checkbox" name="remember" id="remember_me">
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>
            <button type="submit" class="btn btn-danger btn-lg btn-block">{{ __('LOGIN') }}</button>
            <div class="bottom">
                @if (Route::has('password.request'))
                    <span class="helper-text m-b-10"><i class="fa fa-lock"></i>
                        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                    </span><br>
                @endif
                <span>Don't have an account? <a href="{{ route('register') }}">Register</a></span>
            </div>
        </form>
    </div>
@endsection
