<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Cranium</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-purple.min.css')}}">

        <style>
            @media (min-width: 1024px){
                .auth-layout-wrap .auth-content {
                    min-width: 500px !important;
                }
            } 
        </style>
    </head>

    <body>
        <div class="auth-layout-wrap" style="background-image: url({{asset('assets/images/photo-wide-4.jpg')}})">
            <div class="auth-content">
                <div class="card o-hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="p-4">
                                <div class="auth-logo text-center mb-4">
                                    <h3>Cranium</h3>
                                </div>
                                <h1 class="mb-3 text-18">Sign In</h1>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                <div class="form-group">
                                        <label for="username">User Name / Email</label>
                                        <input id="username"
                                            class="form-control form-control-rounded @error('username') is-invalid @enderror"
                                            name="username" value="{{ old('username') }}" autocomplete="username"
                                            autofocus>
                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>                                    
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" id="password" class="form-control form-control-rounded @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group ">
                                        <div class="">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    id="remember" {{ old('remember') ? 'checked' : '' }}>

                                                <label class="form-check-label" for="remember">
                                                    {{ __('Remember Me') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-rounded btn-primary btn-block mt-2">Sign In</button>

                                </form>
                                @if (Route::has('password.request'))

                                <div class="mt-3 text-center">

                                    <a href="{{ route('password.request') }}" class="text-muted"><u>Forgot
                                            Password?</u></a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>

        <script src="{{asset('assets/js/script.js')}}"></script>
    </body>

</html>