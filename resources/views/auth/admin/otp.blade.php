<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>AdminLTE 4 | Login Page v2</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="AdminLTE 4 | Login Page v2">
    <meta name="author" content="ColorlibHQ">
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard">
    <!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}"><!--end::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('css/otp.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
</head> <!--end::Head--> <!--begin::Body-->

<body class="login-page bg-body-secondary">
    <div class="login-box">




        <div class="height-100 d-flex justify-content-center align-items-center">
            <div class="position-relative">
                <p class="login-box-msg">Type Email or Mobile</p>
                <div class="card p-2 text-center">
                    <h6>Please enter the verification code</h6>
                    {{-- <div> <span>A code has been sent to</span> <small id="maskedNumber">*******9897</small> </div> --}}
                    <form action="{{ route('validateOtp') }}" method="post">
                        @csrf
                        <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">

                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="first" required
                                    maxlength="1" />
                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="second" required
                                    maxlength="1" />
                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="third" required
                                    maxlength="1" />
                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="fourth" required
                                    maxlength="1" />
                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="fifth" required
                                    maxlength="1" />
                                <input class="m-2 text-center form-control rounded" type="text" name="otp[]" id="sixth" required
                                    maxlength="1" />
                        </div>

                        <div id="timer" class="mt-3">
                            <span>Resend OTP in <span id="countdown">3:00</span></span>
                        </div>

                        <div class="mt-4">
                            <input type="hidden" name="uuid" value="{{ $admin->id ?? '' }}">
                            <button id="validateBtn" class="btn btn-primary px-4 validate">Validate</button>
                        </div>
                    </form>
                    <div id="resend" class="mt-3" style="display: none;">
                        <form action="{{ route('otpLoad') }}" method="post">
                            @csrf
                            <input type="hidden" name="uuid" value="{{ $admin->id ?? '' }}">
                            <button type="submit" class="btn btn-secondary px-4">Resend OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>




    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('js/adminlte.js') }}"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ asset('js/otp.js') }}"></script>
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!--end::Script-->
</body><!--end::Body-->

<script>
    @if (Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}"
        switch (type) {
            case 'info':

                toastr.options.timeOut = 5000;
                toastr.info("{{ Session::get('message') }}");
                break;
            case 'success':

                toastr.options.timeOut = 5000;
                toastr.success("{{ Session::get('message') }}");

                break;
            case 'warning':

                toastr.options.timeOut = 5000;
                toastr.warning("{{ Session::get('message') }}");
                break;

            case 'error':

                toastr.options.timeOut = 5000;
                toastr.error("{{ Session::get('message') }}");
                s
                break;
        }
    @endif
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timer = 10; // 3 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const resendDiv = document.getElementById('resend');
        const timerInterval = setInterval(function() {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            timer--;

            if (timer < 0) {
                clearInterval(timerInterval);
                document.getElementById('timer').style.display = 'none';
                resendDiv.style.display = 'block';
            }
        }, 1000);
    });
</script>

</html>
