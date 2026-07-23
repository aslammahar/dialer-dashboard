@extends('layouts.login')
@php
$logo = asset(Storage::url('uploads/logo/'));
$company_logo = Utility::getValByName('company_logo');
$settings = Utility::settings();

@endphp

<style>
    .alert {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .alert-danger {
        background-color: #fff5f5;
        border-color: #feb2b2;
        color: #c53030;
    }
    
    .alert-danger .btn-close {
        color: #c53030;
    }
    
    .alert i {
        font-size: 1.25rem;
    }
    
    .alert strong {
        display: block;
        margin-bottom: 0.25rem;
    }
</style>

@push('custom-scripts')
    @if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
@section('page-title')
    {{__('Login')}}
@endsection

@section('content')

    <body class="">
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    <!-- Navbar -->

                    <!-- End Navbar -->
                </div>
            </div>
        </div>
        <main class="main-content  mt-0">
            <section>
                <div class="page-header min-vh-100">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                                <div class="card card-plain">
                                    <div class="card-header pb-0 text-start">
                                        <h4 class="font-weight-bolder">Sign In</h4>
                                        <p class="mb-0">Enter your email and password to sign in</p>
                                    </div>


                                    <div class="card-body">
                                        <form role="form" action="{{ route('login') }}" method="post">
                                            @csrf

                                            @if (session('error'))
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-exclamation-circle me-2"></i>
                                                        <div>
                                                            <strong>Error!</strong> {{ session('error') }}
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label for="email" class="form-label">{{__('Email')}}</label>
                                                <input type="email"
                                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email') }}" required
                                                    autocomplete="email" autofocus>
                                                @error('email')
                                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="mb-3">
                                                <label for="password" class="form-label">{{__('Password')}}</label>
                                                <input type="password"
                                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                    id="password" name="password" required autocomplete="current-password">
                                                @error('password')
                                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="text-center">
                                                <button type="submit"
                                                    class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">{{__('Sign in')}}</button>
                                            </div>
                                        </form>
                                        @if (session('suspension_message'))
                                            <script type="text/javascript">
                                                window.onload = function () {
                                                    alert("{{ session('suspension_message') }}");
                                                    window.location.href = "{{ route('login') }}";
                                                };
                                            </script>
                                        @endif

                                        @if (session('termination_message'))
                                            <script type="text/javascript">
                                                window.onload = function () {
                                                    alert("{{ session('termination_message') }}");
                                                    window.location.href = "{{ route('login') }}";
                                                };
                                            </script>
                                        @endif

                                    </div>
                                    <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}"
                                                class="text-xs text-primary">{{ __('Forgot Your Password?') }}</a>
                                        @endif
                                        <p class="mb-4 text-sm mx-auto">
                                            Don't have an account?
                                            <a href="javascript:;" class="text-primary text-gradient font-weight-bold">Sign
                                                up</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                                <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                    style="background-image: url('https://media.licdn.com/dms/image/C4D0BAQG9Gd_IAtpr2w/company-logo_200_200/0/1645822404237/jsons_communications_logo?e=1709164800&v=beta&t=oXOsatjb2J-bx-TQ87n5rK_iXSFeSdJ39WAD0XMVuLU');
                  background-size: cover;">
                                    <span class="mask bg-gradient-primary opacity-6"></span>
                                    <h4 class="mt-5 text-white font-weight-bolder position-relative">Partner CRM</h4>
                                    <p class="text-white position-relative">This is the CRM for a US BPO specializing in final expense lead generation and closing. Manage leads, closers, recordings, and operations in one place.</p>

                                    <div class="mt-5">
                                        <div class="row">
                                            <div class="col-6">
                                                <a href="#" class="btn btn-outline-light w-100 btn-sm mb-0">Facebook</a>
                                            </div>
                                            <div class="col-6">
                                                <a href="#" class="btn btn-outline-light w-100 btn-sm mb-0">Linkedin</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-------------- ReCaptcha start---------}}
                    @if(env('RECAPTCHA_MODULE') == 'yes')
                        <div class="form-group">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <span class="small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    {{-------------- ReCaptcha end---------}}




                    <!--  @if($settings['enable_signup'] == 'on')
                            <div class="or-text">{{__('OR')}}</div>
                        @endif
                        {{Form::close()}}  -->

                </div>


            </section>
        </main>
@endsection

    @push('script-page')
    <script>
        $(document).ready(function () {
            @if($errors->any())
                @if(Session::has('error'))
                    toastr.error('{{__('Your account has been deactivated.')}}');
                @else
                    toastr.error('{{__('Invalid Credential')}}');
                @endif
            @endif
            });

        // block xss attack
        $(document).ready(function () {
            $('#email').on('keypress', function (event) {
                var regex = new RegExp("^[a-zA-Z0-9@.]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });
            $('#password').on('keypress', function (event) {
                var regex = new RegExp("^[a-zA-Z0-9!@#$%^&*()_+=-]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            $("#forgot-password").click(function () {
                $("#forgot-password-modal").modal('show');
            });
        });
    </script>