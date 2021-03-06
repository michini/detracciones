<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('js/angular-toastr-master/dist/angular-toastr.min.css')}}">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('js/toastr-master/build/toastr.min.css')}}">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    {{-- @if(Auth::check()) 
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Proveedores <span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="{{route('proveedores.create')}}"><i class="glyphicon glyphicon-plus-sign"></i> Nuevo</a></li>
                            <li><a href="{{route('proveedores.index')}}"><i class="glyphicon glyphicon-th"></i> Listar</a></li>
                            <!--<li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">One more separated link</a></li>-->
                          </ul>
                        </li>
                    </ul>
                    @endif --}}

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="glyphicon glyphicon-log-out"></i>
                                            Salir
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/excel-js/cpexcel.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/excel-js/jszip.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/excel-js/shim.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/excel-js/xlsx.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/angular.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/angular-file-saver.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/angular-file-saver.bundle.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/blob.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/xls_module.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/xlsx.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular/angular-js-xls.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular-toastr-master/dist/angular-toastr.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/angular-toastr-master/dist/angular-toastr.tpls.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/toastr-master/build/toastr.min.js')}}"></script>
    @yield('js')
</body>
</html>
