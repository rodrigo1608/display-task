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

                        <div class="col-md-8 d-flex justify-content-end">

                            <form action="{{ route('home') }}" method="get"> @csrf

                                <div class="d-flex">

                                    <input type="date" id="input-date" name="specific_date" class="form-control fs-6"
                                        value="{{ old('specific_date', request()->input('specific_date', Carbon\Carbon::now()->format('Y-m-d'))) }}">

                                    <button type="submit" class="btn btn-secondary ms-1 py-0">
                                        Enviar
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                    <div class="row mt-5">

                        <div class='col-md-8'>
                            <h2 class="fs-5 me-1">{{ $labelOverview }}</h2>
                        </div>

                    </div>

                    <div class="row">

                        <div class='col-md-8'>

                            <div class="accordion" id="accordionFlushExample">

                                @foreach ($selectedCurrentUserTasks as $index => $task)
                                    <div class="accordion-item">

                                        <h2 class="accordion-header">

                                            <button class="accordion-button poppins-semibold collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#flush-collapse{{ $index }}" aria-expanded="false"
                                                aria-controls="flush-collapseOne">

                                                {{ $task->title }} - {{ $task->start }} até
                                                {{ $task->end }}

                                            </button>

                                        </h2>

                                        <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse">

                                            <div class="accordion-body">

                                                <p class="roboto-light fs-5">
                                                    {{ $task->reminder->notification_message }}
                                                </p>

                                                @if ($task->isNotificationTimeMissing)
                                                    <p class="text-danger roboto fs-6">Crie um lembrete para ser
                                                        avisado
                                                        antecipadamente, <a href="#"
                                                            class="roboto-bold text-danger">clique
                                                            aqui.</a></p>
                                                @endif

                                                <p class="roboto"><span class="poppins-medium">Local:</span>
                                                    {{ $task->local }}
                                                </p>

                                                <p class="roboto"><span class="poppins-medium">Criado por:</span>
                                                    {{ $task->creator->name }} {{ $task->creator->lastname }}
                                                </p>

                                                <p class="roboto"><span class="poppins-medium">Participantes:</span>
                                                    {{ $task->emailsParticipants }}
                                                </p>

                                                <p class="roboto">
                                                    {!! $task->recurringMessage !!}
                                                </p>

                                            </div>

                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>

                        @if ($isThereAnyReminder)
                            <div class='col-md-3 offset-1 text-start'>

                                <h2 class="fs-5 poppins-medium" style="color:{{ auth()->user()->color }}">
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

                </div>

            </div>

        </div>



    </div>
@endsection
