<x-mail::message>
{{ $creator }} te convidou para: {{ $task->title }}

<x-mail::button :url="$url">
Clique aqui para ver a tarefa
</x-mail::button>

Att,<br>
{{ config('app.name') }}
</x-mail::message>
