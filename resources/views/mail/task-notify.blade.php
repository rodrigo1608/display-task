<x-mail::message>
{{ $taskData['message'] }}

<x-mail::button :url="$url">
Clique aqui para ver a tarefa
</x-mail::button>

Att,<br>
{{ config('app.name') }}
</x-mail::message>
