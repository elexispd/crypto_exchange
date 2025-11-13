<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Admin Login</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%) !important;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-container {
            padding: 2rem 0;
            text-align: center;
        }

        .logo-img {
            max-width: 500px;
            max-height: unset !important;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e1e5eb;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8f9fc;
            border: 1px solid #e1e5eb;
        }

        .section-title {
            color: #2e384d;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        /* Background animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(-45deg, #f5f7fa, #e4e8f0, #f0f4f8, #e8ecf1);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem 1.5rem;
            }

            .logo-img {
                max-width: 150px;
            }
        }
    </style>
</head>

<body>
    <!-- Background Animation -->
    <div class="bg-animation"></div>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <!-- Logo Section -->
                            <div class="logo-container">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <img src="{{ asset('assets/img/logo.png') }}" style="width: 250px;" class="logo-img" alt="Company Logo">
                                </a>
                            </div><!-- End Logo -->

                            <!-- Login Card -->
                            <div class="card login-container">
                                <div class="card-body">
                                    <!-- Header -->
                                    <div class="text-center mb-4">
                                        <h5 class="section-title">Welcome Back</h5>
                                        <p class="text-muted">Sign in to your account</p>
                                    </div>

                                    <!-- Login Form -->
                                    <form class="row g-3 needs-validation" method="post" novalidate>
                                        @csrf

                                        <!-- Error Messages -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <i class="bi bi-exclamation-octagon me-1"></i>
                                                <span>Invalid Login Details</span>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                            </div>
                                        @endif

                                        <!-- Email Field -->
                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Email Address</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="bi bi-envelope"></i>
                                                </span>
                                                <input type="email" name="email" class="form-control"
                                                    id="yourUsername" placeholder="Enter your email" required>
                                                <div class="invalid-feedback">Please enter a valid email address.</div>
                                            </div>
                                        </div>

                                        <!-- Password Field -->
                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="passwordPrepend">
                                                    <i class="bi bi-lock"></i>
                                                </span>
                                                <input type="password" name="password" class="form-control"
                                                    id="yourPassword" placeholder="Enter your password" required>
                                                <div class="invalid-feedback">Please enter your password.</div>
                                            </div>
                                        </div>

                                        <!-- Remember Me & Forgot Password -->
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remember"
                                                        value="true" id="rememberMe">
                                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                                </div>
                                                <a href="#" class="text-decoration-none small text-primary">
                                                    Forgot password?
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                                Sign In
                                            </button>
                                        </div>

                                        <!-- Register Link (if needed) -->
                                        <!--
                                        <div class="col-12 text-center">
                                            <p class="small mb-0">Don't have an account?
                                                <a href="#" class="text-decoration-none">Create one</a>
                                            </p>
                                        </div>
                                        -->
                                    </form>
                                </div>
                            </div><!-- End Login Card -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main><!-- End #main -->

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
