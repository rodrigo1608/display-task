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

            .feedback-border {
                border: 0.2rem solid {{ Auth::user()->color }};
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

            <div class="container-fluid">

                @if (Auth::check())
                    <button class="btn btn-primary ms-5" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">+</button>
                @endif

                <a class="navbar-brand poppins-regular ms-5 font-black" href="{{ url('/') }}">
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

                                <button class="btn btn-secondary ms-5" type="button" id="dropdownMenuButtonEvent"
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
                            <form action="{{ route('search_tasks.searchByTitle') }}" method="get"
                                class="d-flex me-5 flex-row">
                                @csrf
                                <input type="text" name="title_filter" id="title_filter"
                                    placeholder="Procurar por nome da tarefa"class="form-control fs-6 me-1">
                                <button class="btn btn-primary">Enviar</button>
                            </form>

                            @php
                                $hasAnyPendingTask = count($pendingTasks) > 0;

                                $pluralOrSingularInvitation =
                                    count($pendingTasks) > 1 ? 'convites pendentes' : 'convite pendente';
                            @endphp

                            @if ($hasAnyPendingTask)
                                <div class="btn-group">

                                    <button type="button" class="btn btn-danger dropdown-toggle me-3"
                                        data-bs-toggle="dropdown" aria-expanded="false">

                                        Você possui {{ count($pendingTasks) }} {{ $pluralOrSingularInvitation }}

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

        @if (session('success'))
            <div id="success-alert" class="row" style="position:absolute; top:10%; left: 50%; z-index: 1000;">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const alertBox = document.getElementById('success-alert');
                if (alertBox) {
                    setTimeout(() => {

                        alertBox.style.transition = 'opacity 1s';

                        alertBox.style.opacity = '0';

                        setTimeout(() => {
                            alertBox.remove();
                        }, 1000);

                    }, 3000);
                }
            });
        </script>

        @if (Auth::check())
            <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
                aria-labelledby="offcanvasWithBothOptionsLabel">

                <div class="w-100 text-end">
                    <button type="button" class="btn-close me-4 mt-4" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-header">

                    <div class="w-100 d-flex flex-column ps-4">

                        <div
                            class="profile-picture-container profile-border rounded-circle d-flex justify-content-center align-items-center overflow-hidden">

                            <img class="img-size" src="{{ asset('storage/' . $user->profile_picture) }}"
                                alt="Imagem do usuário">

                        </div>

                        <div class='mt-2 text-start'>

                            <h2 class="fs-4 poppins-medium m-0 p-0">
                                {{ $user->name }}
                                {{ $user->lastname }}
                            </h2>

                            <p class="fs-5 roboto">{{ $user->role }} </p>

                            <p class="fs-5 roboto m-0 p-0">{{ $user->email }} </p>

                            <p class="fs-5 roboto m-0">{{ $user->telephone }} </p>
                        </div>

                    </div>

                </div>

                <div class="offcanvas-body">

                    <ul class="list-group poppins">

                        @php
                            $today = Carbon\Carbon::today()->format('Y-m-d');
                        @endphp

                        <a href="{{ route('home', ['selectedDate' => $today]) }}" class="side-link">
                            <li class="list-group-item">Meu dia</li>
                        </a>

                        <a href="#" class="side-link">
                            <li class="list-group-item">Minha semana</li>
                        </a>

                        <a href="#" class="side-link">
                            <li class="list-group-item">Meu mês</li>
                        </a>

                        <a href="#" class="side-link">
                            <li class="list-group-item active" aria-current="true">Painel geral</li>
                        </a>

                    </ul>

                    <div class="mt-3">

                        <div class="accordion" id="accordionPanelsStayOpenFilter">

                            <div class="accordion-item">

                                <h2 class="accordion-header">

                                    <button class="accordion-button poppins-regular" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFilter"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseFilter">
                                        Filtrar tarefas
                                    </button>
                                </h2>

                                <form action="" method="get">
                                    @csrf

                                    <div id="panelsStayOpen-collapseFilter" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <div class="form-check">

                                                <input class="form-check-input" type="checkbox" value="true"
                                                    name="created_by_me" id="created_by_me"
                                                    {{ old('created_by_me') === 'true' ? 'checked' : '' }}>

                                                <label class="form-check-label" for="created_by_me">
                                                    Criadas por mim
                                                </label>

                                            </div>

                                            <div class="form-check">

                                                <input class="form-check-input" type="checkbox" value="true"
                                                    name="task_participation" id="task_participation"
                                                    {{ old('task_participation') === 'true' ? 'checked' : '' }}>

                                                <label class="form-check-label" for="task_participation">
                                                    Estou participando
                                                </label>

                                            </div>

                                            <div class="form-check">

                                                <input class="form-check-input" type="checkbox" value="true"
                                                    name="task_participation" id="task_participation"
                                                    {{ old('task_participation') === 'true' ? 'checked' : '' }}>

                                                <label class="form-check-label" for="task_participation">
                                                    Concluídas
                                                </label>

                                            </div>

                                        </div>


                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        @endif

        <main class="">

            @yield('content')

        </main>

    </div>

</body>

</html>
