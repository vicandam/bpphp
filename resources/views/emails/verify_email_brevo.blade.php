@component('mail::message')
# Hello {{ $name }} 👋

Please verify your email address by clicking the button below:

@component('mail::button', ['url' => $verifyUrl])
    Verify Email
@endcomponent

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
