<x-mail::message>
# Hello, {{ $name }}

{{ $messageContent }}

Best regards,<br>
**{{ config('app.name') }}**
</x-mail::message>
