<x-mail::message>
    # Welcome to BPPHP Fun, {{ $user->name ?? $user->email }}!

    Thank you for your ticket purchase! We've automatically created an account for you.

    Here are your login credentials:
    - **Email:** {{ $user->email }}
    - **Password:** {{ $password }}

    Please log in and consider changing your password for security.

    <x-mail::button :url="url('/login')">
        Login to Your Account
    </x-mail::button>

    We are excited to have you as part of the BPPHP Movement!

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
