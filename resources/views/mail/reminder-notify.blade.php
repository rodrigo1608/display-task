<x-mail::message>

{{ $reminder->notification_message }}

Att,<br>
{{ config('app.name') }}
</x-mail::message>
