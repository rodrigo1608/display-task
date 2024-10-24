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

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="z-index:4">

            <div class="container-fluid">

                {{-- botão do offcanvas --}}

                @if (Auth::check())
                    <button class="btn btn-primary ms-5" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"
                        title="Visualizar barra lateral"
                        style=" border: 1px solid lightgrey;
                                border-radius: 0.5rem;">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6" width="1.5em" height="1.5em">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </button>
                @endif

                <a class="navbar-brand poppins-regular ms-5 font-black" href="{{ url('/') }}"
                    title="Clique parar ir para página inicial">
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

                                {{-- Botão de criar eventos --}}

                                <button class="btn btn-secondary ms-5 px-2" type="button" id="dropdownMenuButtonEvent"
                                    data-bs-toggle="dropdown" aria-expanded="false" title="Criar evento">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="size-6" width="1.5em" height="1.2em">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>

                                </button>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonEvent">

                                    @if (auth()->user()->super === 'true')
                                        <li class="">
                                            <a class="dropdown-item" href="{{ route('invitation') }}">
                                                Convidar
                                            </a>
                                        </li>
                                    @endif
                                    <li class="">
                                        <a class="dropdown-item" href="{{ route('reminder.create') }}">
                                            Lembrete
                                        </a>
                                    </li>

                                    @if(auth()->user()->super === 'true')

                                        <!-- Button trigger modal -->
                                        <button type="button" class="
                                            btn
                                            border-0
                                            text-start
                                            rounded-0
                                            dropdown-item
                                            w-100
                                        "
                                        data-bs-toggle="modal" data-bs-target="#assign-task-modal">
                                             Tarefa
                                        </button>

                                    @else

                                        <li class="">

                                            <a class="dropdown-item" href="{{ route('task.create', ['user' => $user->id]) }}">
                                                Tarefa
                                            </a>

                                        </li>

                                    @endif

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
                                    placeholder="Procurar por nome da tarefa"
                                    class="clean-form-control rounded-0 rounded-start border-end-1 fs-6 px-2">

                                <button class="btn btn-primary rounded-end rounded-0 border-1 border-start-0"
                                    style="    border: 1px solid lightgrey;
                                                border-radius: 0.5rem;"
                                    title="Pesquisar">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        width="19" height="19" stroke-width="1.5" stroke="currentColor"
                                        class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>

                                </button>

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

                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
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

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>

                                </div>

                            </li>

                        @endguest

                    </ul>

                </div>

            </div>

        </nav>
        @if(isset($user))
        <!-- Modal Atribuir trefa-->
        <div class="modal fade" id="assign-task-modal" tabindex="-1" aria-labelledby="assign-task-modal" aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <h1 class="modal-title fs-5" id="assign-task-modal-label">Atribuir Tarefa</h1>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>

                    <div class="modal-body d-flex flex-column align-items-start">

                        <a class="user-invitation" href="{{ route('task.create', ['user' => $user->id])}}" >

                            <div class="rounded-circle d-flex justify-content-center align-items-center overflow-hidden"
                                style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $user->color }}"
                                title="{{ $user->name }} {{ $user->lastname }}">

                                <img class="w-100" src="{{ asset('storage/' . $user->profile_picture) }}"
                                    alt="Imagem do usuário">

                            </div>

                            <span class=" text-dark">
                                Para mim
                            </span>

                        </a>


                        @foreach ( $allUsers as $invitationUser )

                            <a class="user-invitation" href="{{ route('task.create', ['user' => $invitationUser->id])}}">

                                <div class="rounded-circle d-flex justify-content-center align-items-center overflow-hidden"
                                    style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $invitationUser->color }}"
                                    title="{{ $invitationUser->name }} {{ $invitationUser->lastname }}">

                                    <img class="w-100" src="{{ asset('storage/' . $invitationUser->profile_picture) }}"
                                        alt="Imagem do usuário">

                                </div>

                                <span class=" text-dark">
                                    {{ $invitationUser->email}}
                                </span>

                            </a>

                        @endforeach

                    </div>

                </div>
            </div>
        </div>

        @endif

        @if (session('success'))

            <div style="position:fixed; left:50%; top:15%; transform: translate(-50%, -50%); z-index: 2;"  data-alert>

                <div id="success-alert" class="row justify-content-center mt-4">

                    <div class="alert alert-success d-flex align-items-center gap-2 fs-4 p-4 text-center">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6 border border-success  border-3 p-1 rounded-circle" width="2em">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                          </svg>

                        <span>{{ session('success') }}</span>

                    </div>

                </div>

            </div>

        @elseIf (session('warning'))


        <div style="position:fixed; left:50%; top:15%; transform: translate(-50%, -50%); z-index: 2;"  data-alert>

            <div id="warning-alert" class="row justify-content-center mt-4">

                <div class="alert fs-4 alert-warning p-4 text-center">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="2em">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                      </svg>


                      <span> {{ session('warning') }}</span>

                </div>

            </div>

        </div>

        @elseIf(session('error'))

        <div style="position:fixed; left:50%; top:15%; transform: translate(-50%, -50%); z-index: 2;"  data-alert>

            <div id="danger-alert" class="row justify-content-center mt-4">


                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="2em">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                  </svg>

                <div class="alert fs-4 alert-danger p-4 text-center">

                    <span>  {{ session('error') }} </span>

                </div>

            </div>

        </div>

        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {

               const alertBox = document.querySelector('[data-alert]');

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
            <div class="offcanvas border-start-0 rounded-end offcanvas-start ps-2"
                data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
                aria-labelledby="offcanvasWithBothOptionsLabel">

                <div class="w-100 text-end">
                    <button type="button" class="btn-close me-4 mt-4" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>

                <div class="offcanvas-header">

                    <div class="w-100 d-flex flex-column ps-4" style="border-bottom:1px solid lightgrey">

                        <div
                            class="profile-picture-container profile-border rounded-circle d-flex justify-content-center align-items-center overflow-hidden">

                            <img class="img-size" src="{{ asset('storage/' . $user->profile_picture) }}"
                                alt="Imagem do usuário">

                        </div>

                        <div class='mt-2 text-start'>

                            <h2 class="fs-2 poppins m-0 mb-3 p-0">
                                {{ $user->name }}
                                {{ $user->lastname }}
                            </h2>

                            <p class="fs-5 roboto-light mb-3">{{ $user->role }} </p>

                            <p class="fs-5 roboto-light m-0 mb-3 p-0">{{ $user->email }} </p>

                            <p class="fs-5 roboto-light m-0 mb-3">{{ $user->telephone }} </p>

                        </div>

                    </div>

                </div>

                <div class="offcanvas-body">

                    <ul class="list-group poppins-extralight fs-5">

                        @php
                            $today = Carbon\Carbon::today()->format('Y-m-d');
                        @endphp

                        <a href="{{ route('display.day') }}"
                            class="side-link w-50 {{ Route::currentRouteName() == 'display.day' ? 'selected poppins' : '' }}">

                            <li class="list-group-item">Meu dia</li>

                        </a>

                        <a href="{{ route('display.week') }}"
                            class="side-link w-50 {{ Route::currentRouteName() == 'display.week' ? 'selected poppins' : '' }}">

                            <li class="list-group-item">Minha semana</li>

                        </a>

                        <a href="{{ route('display.month') }}"
                            class="side-link w-50 {{ Route::currentRouteName() == 'display.month' ? 'selected poppins' : '' }}">

                            <li class="list-group-item">Meu mês</li>

                        </a>

                        <a href="{{ route('home') }}"
                            class="side-link w-50 {{ Route::currentRouteName() == 'home' ? 'selected poppins' : '' }}">

                            <li class="list-group-item" aria-current="true">Meu painel</li>

                        </a>

                        <a href="{{ route('display.panel') }}" class="side-link w-50">

                            <li class="list-group-item" aria-current="true">Painel geral</li>

                        </a>

                    </ul>

                </div>

            </div>
        @endif

        <main class="">

            @yield('content')

        </main>

    </div>

</body>

</html>
