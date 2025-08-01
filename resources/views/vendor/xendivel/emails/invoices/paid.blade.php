<x-mail::message>
# Greetings!

<p>
@if ($customMessage === null)
Thank you for your recent purchase from {{ config('app.name') }}. We have attached your invoice to this email.
@else
{{ $customMessage }}
@endif
</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
