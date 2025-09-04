<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        BPPHP Movement
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <!-- Nucleo Icons -->
    <link href="{{ asset('themes/material-dashboard/assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/material-dashboard/assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('themes/material-dashboard/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet">
</head>

<body class="">
<main class="main-content  mt-0">
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url({{asset('themes/material-dashboard/assets/img/illustrations/illustration-signup.jpg')}}); background-size: cover;">
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                        <div class="card card-plain">
                            <div class="card-header">
                                <h4 class="font-weight-bolder">Sign Up</h4>
                                <p class="mb-0">Enter your email and password to register</p>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="form-group">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Name</label>
                                            <x-text-input id="name" class="block mt-1 w-full form-control" type="text" name="name" :value="old('name')" required autofocus />
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Email</label>
                                            <x-text-input id="email" class="form-control block mt-1 w-full" type="email" name="email" value="{{old('email')}}" required autofocus/>

                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="input-group input-group-outline is-filled mb-4">
                                            <label class="form-label">Password</label>
                                            <x-text-input type="password" class="form-control block mt-1 w-full" name="password" required />
                                        </div>
                                        <div>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <x-text-input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control block mt-1 w-full" />
                                        </div>

                                        <div>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Referral Code</label>
                                            <x-text-input id="referral_code" class="block mt-1 w-full form-control" type="text" name="referral_code" :value="old('referral_code')" autofocus />
                                        </div>
                                    </div>

                                    <div class="form-check form-check-info text-start ps-0">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                                        <label class="form-check-label" for="flexCheckDefault">
                                            I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                                        </label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-lg bg-gradient-dark btn-lg w-100 mt-4 mb-0">Sign Up</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <p class="mb-2 text-sm mx-auto">
                                    Already have an account?
                                    <a href="{{route('login')}}" class="text-primary text-gradient font-weight-bold">Sign in</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!--   Core JS Files   -->
<script src="{{ asset('themes/material-dashboard/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('themes/material-dashboard/assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('themes/material-dashboard/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('themes/material-dashboard/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{asset('themes/material-dashboard/assets/js/material-dashboard.min.js?v=3.2.0')}}"></script>
</body>

</html>
