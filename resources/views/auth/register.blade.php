@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card mt-5 py-5">

                    <div class="card-body">

                        <form method="POST" {{-- action="{{ route('register.createWithRequest') }}" --}}enctype="multipart/form-data">
                            @csrf

                            {{-- Input name --}}
                            <div class="row mb-4">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                        class="input-form-text form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Input lastname --}}
                            <div class="row mb-4">
                                <label for="lastname" class="col-md-4 col-form-label text-md-end">Sobrenome</label>

                                <div class="col-md-6">

                                    <input id="lastname" type="text"
                                        class="form-control @error('lastname') is-invalid @enderror" name="lastname"
                                        value="{{ old('lastname') }}" autocomplete="lastname" autofocus>

                                    @error('lastname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Input role --}}
                            <div class="row mb-4">

                                <label for="role" class="col-md-4 col-form-label text-md-end">Função</label>

                                <div class="col-md-6">

                                    <input id="role" type="text"
                                        class="form-control @error('role') is-invalid @enderror" name="role"
                                        value="{{ old('role') }}" autocomplete="role" autofocus>

                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Input email --}}
                            <div class="row mb-4">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="text"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Input telephone --}}
                            <div class="row mb-4">
                                <label for="telephone" class="col-md-4 col-form-label text-md-end">Telefone</label>

                                <div class="col-md-6">
                                    <input id="telephone" type="text"
                                        class="form-control @error('telephone') is-invalid @enderror" name="telephone"
                                        value="{{ old('telephone') }}" autocomplete="telephone">

                                    @error('telephone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Input Color --}}
                            <div class="row mb-4">

                                <label for="color" class="col-md-4 col-form-label text-md-end">Cor do seu perfil</label>
                                <input id="input-color" name="color" type="hidden" value="{{ old('color') }}">

                                <div class="col-md-6">

                                    <!-- Button trigger modal -->
                                    <button id="btn-chosen-color" type="button"
                                        class="btn btn-primary btn-outline-dark @error('color') is-invalid @enderror"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Escolha a cor do seu perfil
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">

                                        <div class="modal-dialog">

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h1 class="modal-title fs-5 poppins-regular" id="myModalLabel">
                                                        Escolha a cor
                                                    </h1>



                                                    <button type="button"
                                                        class="btn-close btn-outline-dark focus-ring-dark"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body d-flex flex-wrap">

                                                    @foreach ($avaliableColors as $color)
                                                        <div class="color-container rounded"
                                                            style="background-color: {{ $color }}"
                                                            data-color="{{ $color }}">
                                                        </div>
                                                    @endforeach

                                                </div>

                                                <div class="modal-footer">

                                                    <button id="btn-save" type="button" data-bs-dismiss="modal"
                                                        class="btn btn-primary btn-outline-dark">Confirmar</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        const myModal = document.getElementById('exampleModal')

                                        const btnSave = document.getElementById('btn-save')
                                        const btnChosenColor = document.getElementById('btn-chosen-color')

                                        const inputColor = document.getElementById('input-color')

                                        const myInput = document.getElementById('myInput')

                                        myModal.addEventListener('shown.bs.modal', () => {

                                            const colorsContainers = document.querySelectorAll('.color-container')

                                            colorsContainers.forEach(colorContainer => {

                                                colorContainer.addEventListener('click', event => {

                                                    colorsContainers.forEach(container => container.classList.remove('focused'))

                                                    const clickedElement = event.target

                                                    clickedElement.classList.add('focused')

                                                    const color = clickedElement.getAttribute('data-color')

                                                    inputColor.value = color

                                                    btnChosenColor.style.backgroundColor = color

                                                    btnChosenColor.style.color = 'white'
                                                })

                                            });

                                        })
                                    </script>

                                    @error('color')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Image profile input --}}

                            <div class="row mb-4">

                                <label for="formFile" class="col-md-4 col-form-label text-md-end">Imagem do
                                    perfil</label>

                                <div class="col-md-6">
                                    <input name="profile_picture" class="form-control" type="file"
                                        id="profile_picture" value="{{ old('profile_picture"') }}">
                                </div>

                                @error('profile_picture')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                            {{-- Input password --}}
                            <div class="row mb-4">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password" value="{{ old('name') }}">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="password-confirm"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-outline-dark">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection