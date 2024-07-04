<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tela Tarefa') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- Material icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    {{-- styles --}}

    @if (Auth::check())
        <style>
            .profile-border {
                border: 0.4rem solid {{ Auth::user()->color }};
            }

            .reminders-link {
                color: {{ Auth::user()->color }}
            }

            .reminders-link:hover {
                font-weight: 500;
            }
        </style>
    @endif

</head>

<body class="poppins-regular">

    <div id="app">

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">

            <div class="container">

                <a class="navbar-brand poppins-regular font-black" href="{{ url('/') }}">
                    {{ config('app.name', 'Tela tarefa') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                        @auth
                            <div class="dropdown">

                                <button class="btn btn-secondary" type="button" id="dropdownMenuButtonEvent"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Criar evento
                                </button>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonEvent">

                                    <li class="">
                                        <a class="dropdown-item" href="{{ route('reminder.create') }}">
                                            Lembrete
                                        </a>
                                    </li>

                                    <li class="">
                                        <a class="dropdown-item" href="{{ route('task.create') }}">
                                            Tarefa
                                        </a>
                                    </li>
                                </ul>

                            </div>
                        @endauth

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">

                        @auth
                            @php
                                $hasAnyPendingTask = count($pendingTasks) > 0;

                                $pluralOrSingularInvitation =
                                    count($pendingTasks) > 1 ? 'convites pendentes' : 'convite pendente';
                            @endphp

                            @if ($hasAnyPendingTask)
                                <div class="btn-group">

                                    <button type="button" class="btn btn-danger dropdown-toggle me-3"
                                        data-bs-toggle="dropdown" aria-expanded="false">

                                        Voce possui {{ count($pendingTasks) }} {{ $pluralOrSingularInvitation }}

                                    </button>

                                    <ul class="dropdown-menu">

                                        @foreach ($pendingTasks as $task)
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('task.show', ['task' => $task->id, 'view' => 'pending']) }}">{{ $task->title }}
                                                    - {{ $task->creator->name }}
                                                    {{ $task->creator->lastname }} </a>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>
                            @endif

                        @endauth

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    @auth
                                        <a href="{{ route('user.edit', auth()->id()) }}" class="dropdown-item">Editar
                                            perfil</a>
                                    @endauth

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                                </div>

                            </li>

                        @endguest

                    </ul>

                </div>

            </div>

        </nav>

        <main class="py-4">

            @yield('content')

        </main>

    </div>

</body>

</html>
