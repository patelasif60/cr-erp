<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Cranium</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
        @yield('before-css')
        {{-- theme css --}}
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link id="gull-theme" rel="stylesheet" href="{{  asset('assets/styles/css/themes/lite-purple.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/toastr.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/css/custom.css')}}">
        @if (Session::get('layout')=="vertical")
        <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-5.10.1-web/css/all.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/styles/vendor/metisMenu.min.css') }}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
        @endif
        <link rel="stylesheet" href="{{asset('assets/styles/css/select2/new/select2.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/css/flatpicker/flatpicker.min.css')}}">
        {{-- page specific css --}}
        <script src="{{  asset('assets/js/common-bundle-script.js')}}"></script>
        <script src="{{asset('assets/js/jquery-ui.min.js')}}"></script>
        <style>
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Firefox */
            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
        @yield('page-css')
    </head>
    <body class="text-left">

        <!-- Pre Loader Strat  -->
        <div class='loadscreen' id="preloader">
            <div class="loader spinner-bubble spinner-bubble-primary">
            </div>
        </div>

        <div class="app-admin-wrap layout-sidebar-large clearfix">
            @include('layouts.header-menu')

            @include('layouts.sidebar')

            <div class="main-content-wrap sidenav-open d-flex flex-column">
                <div class="main-content">

                    @if(session()->has('success'))
                    <div class="alert alert-success">
                        <strong>Success!</strong> {!! \Session::get('success') !!}
                    </div>
                    @elseif(session()->has('error'))
                    <div class="alert alert-danger">
                        <strong>Error!</strong> {!! \Session::get('error') !!}
                    </div>
                    @elseif(session()->has('warning'))
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> {!! \Session::get('warning') !!}
                    </div>
                    @endif

                    @yield('main-content')
                </div>

                @include('layouts.footer')
            </div>
        </div>
        <div id="GeneralModal" class="modal fade" role="dialog">
        </div>
        <div class="modal fade" id="MyModalwizard" data-backdrop="static">
        </div>
        @include('layouts.search')

        {{-- common js --}}
        
        {{-- page specific javascript --}}
        @yield('page-js')
        {{-- theme javascript --}}
        {{-- <script src="{{mix('assets/js/es5/script.js')}}"></script> --}}
		<script src="https://kit.fontawesome.com/385e462e48.js" crossorigin="anonymous"></script>
        <script src="{{asset('assets/js/script.js')}}"></script>
        
        <script src="{{asset('assets/js/sidebar.large.script.js')}}"></script>
        <script src="{{asset('assets/js/customizer.script.js')}}"></script>
        <script src="{{ asset('assets/js/tooltip.script.js') }}"></script>
        <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
        <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
        <script src="{{asset('assets/js/vendor/toastr.min.js')}}"></script>
        <script src="{{asset('assets/custom/js/custom.js')}}"></script>
        <script src="{{asset('assets/js/select2/new/select2.min.js')}}"></script>
        <script src="{{ asset('assets/js/validation/jquery.validate.js') }}"></script>
        <script src="{{ asset('assets/js/validation/additional-methods.min.js') }}"></script>
        <script src="{{asset('assets/js/flatpicker/flatpicker.js')}}"></script>
        <script src="{{asset('assets/js/jquery.inputmask.bundle.min.js')}}"></script>

        {{-- laravel js --}}
        {{-- <script src="{{mix('assets/js/laravel/app.js')}}"></script> --}}

        @include('layouts.live-feedback')

        @include('layouts.help')


        @yield('bottom-js')
        <script>
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 2000
            };
        </script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function OpenModel(url){
                $.ajax({
                    url:url,
                    method:'GET',
                    dataTye:'html',
                    success:function(res){
                        $("#GeneralModal").html(res);
                        $("#GeneralModal").modal('show');
                    }
                })
            }

            $('.select2').select2();

            function GetModel(url){
                $.ajax({
                    url:url,
                    method:'GET',
                    success:function(res){
                        $("#MyModalwizard").html(res);
                        $("#MyModalwizard").modal();
                    }
                });
            }

            function MarkAsRead(id){
                $.ajax({
                    url:'{{url('MarkAsRead')}}/'+id,
                    method:'GET',
                    success:function(res){
                        
                    }
                });
            }
            // $('.select2').select2({
            //     dropdownParent: $('#MyModalwizard')
            // });

            $(".sidebar-overlay").hover(function () {
                $(".sidebar-overlay").removeClass('open');
                $(".sidebar-left-secondary").removeClass('open');
            })

            $('.flatpickr').flatpickr();

            $('.datepicker_dmy').flatpickr({
                dateFormat: "d-m-Y",
            });

            $(function(){
                //  Inputmask("9{2}[-]9{2}[-]9{4}", {
                //     placeholder: "-",
                //     greedy: false
                // }).mask('.date-time');
            })
        </script>

    </body>
</html>
