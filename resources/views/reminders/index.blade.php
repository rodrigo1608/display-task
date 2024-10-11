@extends('layouts.app')

@section('content')
    <div class="fs-5 container">

        <div class="row mt-5">

            @if (@$reminders->isEmpty())
                <div class="col-md-8 offset-2">
                    <div class="alert alert-info text-center" role="alert">
                        Nenhum lembrete disponível no momento
                    </div>

                </div>
            @else
                <div class="col-md-10">
                    <table class="table-hover form-input table rounded align-middle">

                        <thead class="">
                            <tr>
                                <th scope="col" class="poppins-regular fs-6 ps-2">
                                    Título
                                </th>
                                <th scope="col" class="poppins-regular fs-6 ps-2">
                                    Recorrência
                                </th>
                                <th scope="col" class="poppins-regular fs-6 ps-2">
                                    Notificação
                                </th>
                                <th scope="col"></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($reminders as $reminder)
                                <tr>

                                    <td class="roboto-light fs-6 bg-white py-4 ps-2"> {{ $reminder->title }}</td>

                                    @php

                                        $recurring = $reminder->recurring;

                                        $recurrinMessage = getRecurringMessage($recurring);

                                    @endphp


                                    <td class="roboto-light fs-6 bg-white ps-2">{!! $recurrinMessage !!}</td>

                                    <td class="roboto-light fs-6 bg-white ps-2">{{ $reminder->notification_message }}</td>

                                    <form action="{{ route('reminder.destroy', ['reminder' => $reminder->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <td class="bg-white text-center">
                                            <button type="submit"
                                                class="btn rounded-circle btn-outline-danger poppins-regular ms-2 border-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    width="1.2em" height="24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg></button>
                                        </td>
                                    </form>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            @endif


        </div>

        <div class="mt-5 text-end" style="">

            {{-- botão de voltar --}}

            <a class="btn btn-primary me-3" aria-label="Voltar para a pagina inicial" href="{{ route('home') }}"
                title="voltar">

                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
            </a>

        </div>


    </div>
@endsection
