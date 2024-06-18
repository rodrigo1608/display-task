@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="row vh-100">

            <div class="col-md-2 border-end">

                <ul class="list-group poppins">

                    <a href="#" class="side-link">
                        <li class="list-group-item">Meu Perfil</li>
                    </a>

                    <a href="#" class="side-link">
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

            </div>

            <div class="col-md-9 container">

                <div class="card text-center">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Active</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Link</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <h1>{{ $task }}</h1>
                    </div>
                </div>


            </div>

        </div>

    </div>

    </div>
@endsection
