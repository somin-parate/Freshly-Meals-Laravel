@extends('layouts.app')

@section('content')
<div class="login">
    <div class="row justify-content-center">
        <div class="col-md-12">
        <div class="card login-card"> 
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6">   
                <div class="card-body">

                <div class="logo__image">
                        <img src="{{ asset('images/freshly.png') }}" class="img-fluid"> 
                    </div>

                    <div class="login__form">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="submit-btn btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                <!-- @if (Route::has('password.request')) -->
                                    <!-- <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif -->
                            </div>
                        </div>
                    </form>
</div>
                </div>
            </div>

                <div class="col-lg-6">
                    <div class="login__image">
                        <img src="{{ asset('images/img2.jpg') }}" class="img-fluid"> 
                    </div>
                    <div class="login__heading">
                        <h2 class="title">FRESHLY MEALS</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vestibulum iaculis purus, ut viverra velit faucibus eu.</p>
                    </div>
                </div>
         

            </div>           
            </div>
        </div>
    </div>
</div>
@endsection
