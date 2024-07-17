@extends('layouts.app')

@section('content')
    <div class="fs-5 container">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card mt-5">

                    <div class="card-header">

                        <h5 class="fs-2 poppins-regular" id="createReminderkLabel">Criar
                            observação
                        </h5>

                    </div>

                    <div class="card-body my-5 px-5">

                        <form method="POST" action="{{ route('reminder.store') }}">
                            @csrf
                            <div class="row mt-3">

                                <div class="">
                                    <label for="title" class="fs-6">Mensagem de notificação</label>
                                    <textarea name="notification_message" id="" cols="30" rows="5" class="form-control roboto">{{ old('notification_message') }}</textarea>
                                </div>

                            </div>

                            <div class="d-flex justify-content-between mt-3">

                                <a class="btn btn-primary" href="{{ route('home') }}">voltar</a>

                                <button type="submit" class="btn btn-secondary">Salvar lembrete</button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    @endsection
