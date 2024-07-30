<x-mail::message>
    # Introduction

    Sua tarefa XXXXXXX irá começar em XXXXX.

    <x-mail::button :url="''">
        Ver tarefa
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
