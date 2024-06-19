@extends('layouts.app')

@section('content')
    <div class="fs-5 container">

        <div class="row justify-content-center">

            <table class="table-hover form-input table rounded border align-middle">

                <thead class="text-center">
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Notificação</th>
                        <th scope="col">Descrição</th>
                        <th scope="col"></th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($reminders as $reminder)
                        <tr class="text-center">
                            <td>{{ $reminder->title }}</td>

                            @php
                                $hasSpecificDate = !empty($reminder->notification);

                                $date = null;
                                $time = null;

                                if ($hasSpecificDate) {
                                    $dateTime = date('d/m/Y H:i:s', strtotime($reminder->notification));
                                    $date = explode(' ', $dateTime)[0];
                                    $time = explode(' ', $dateTime)[1];
                                    $formatedTime = substr($time, 0, -3);
                                    $time = $formatedTime;
                                } else {
                                    $recurringAttributes = $reminder->reminderRecurring->getAttributes();

                                    $recurringDays = array_filter($recurringAttributes, function ($attribute) {
                                        return $attribute == 'true';
                                    });

                                    $isEveryDay = count($recurringDays) == 7;
                                }

                            @endphp

                            @if ($hasSpecificDate)
                                <td class="word-wrap-break text-center">{{ $date }} às {{ $time }}</td>
                            @elseif ($isEveryDay)
                                <td class="word-wrap-break text-center">
                                    Todos os dias às {{ substr($reminder->reminderRecurring->notification_time, 0, 5) }}
                                </td>
                            @else
                                <td class="">
                                    @foreach ($recurringDays as $day => $recurringDay)
                                        @switch($day)
                                            @case('sunday')
                                                <span class="me-4 mt-2 text-center"> domingo</span>
                                            @break

                                            @case('monday')
                                                <span class="me-4 mt-2"> segunda</span>
                                            @break

                                            @case('tuesday')
                                                <span class="me-4 mt-2"> terça</span>
                                            @break

                                            @case('wednesday')
                                                <span class="me-4 mt-2"> quarta</span>
                                            @break

                                            @case('thursday')
                                                <span class="me-4 mt-2"> quinta</span>
                                            @break

                                            @case('friday')
                                                <span class="me-4 mt-2"> sexta</span>
                                            @break

                                            @case('saturday')
                                                <span class="me-4 mt-2"> sabado</span>
                                            @break
                                        @endswitch
                                    @endforeach

                                    <div class="me-4 mt-2"> às
                                        {{ substr($reminder->reminderRecurring->notification_time, 0, 5) }}</span>
                                </td>
                            @endif


                            <td>{{ $reminder->description }}</td>


                            <form action="{{ route('reminder.destroy', ['reminder' => $reminder->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <td>
                                    <button type="submit" class="btn btn-secondary">Remover</button>

                                </td>
                            </form>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>
        <div class="d-flex justify-content-end">
            <a class="btn btn-primary" href="{{ route('home') }}">voltar</a>
        </div>


    </div>
@endsection
