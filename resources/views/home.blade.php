@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="row vh-100">

            <div class="col-md-9 container" style="position:relative">

                <div class="row">

                    @if (session('success'))
                        <div id="success-alert" class="row" style="position:absolute; left: 20%;">
                            <div class="alert alert-success col-md-4">
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

                    <div class="row mt-5">
                        <h2 class="fs-4">Visão Geral</h2>
                    </div>

                    {{-- <div class='col-md-8'>

                        <div class="accordion accordion-flush" id="accordionFlushExample">

                            <div class="d-flex flex-row">
                                <form action="" method="get">

                                    @csrf

                                    <select class="form-select form-select-sm border-none"
                                        aria-label="Small select example">
                                        <option selected disabled>Filtrar por recorrência</option>
                                        <option value="spec">Dias específicos</option>
                                        <option value="sun">Domingos</option>
                                        <option value="mon">Segundas</option>
                                        <option value="tues">Terças</option>
                                        <option value="wed">Quartas</option>
                                        <option value="thurs">Quintas</option>
                                        <option value="fri">Sextas</option>
                                        <option value="satur">Sábados</option>
                                    </select>
                                </form>

                                <button class="btn btn-primary ms-1 py-0">
                                    Filtrar
                                </button>
                            </div>

                            @foreach ($myTasks as $index => $myTask)
                                <div class="accordion-item">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapse{{ $index }}" aria-expanded="false"
                                            aria-controls="flush-collapseOne">
                                            @php

                                            @endphp
                                            {{ $myTask->reminder->recurring }}
                                        </button>

                                    </h2>
                                    <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">Placeholder content for this accordion, which is
                                            intended to
                                            demonstrate the <code>.accordion-flush</code> class. This is the first item's
                                            accordion body.</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}

                    @if ($isThereAnyReminder)
                        <div class='col-md-2 text-start'>

                            <h2 class="fs-6 poppins-medium" style="color:{{ $currentUser->color }}">
                                Lembretes
                            </h2>

                            <ul class="roboto" style="list-style-type: circle">
                                @foreach ($currentUserReminders->take(5) as $reminder)
                                    <li class="">{{ $reminder->title }}</li>
                                @endforeach
                            </ul>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('reminder.index') }}" class="userReminders-link">
                                    {{ $currentUserReminders->count() > 5 ? 'Ver todos lembretes' : ' Ver detalhes' }}
                                </a>
                            </div>

                        </div>
                    @endif

                </div>

                <div class="row">
                    <div class="col-md-8 p-0">

                        <div class="row">

                        </div>

                    </div>
                </div>
            </div>

        </div>


    </div>

    </div>

    </div>

    </div>
@endsection
