<x-mail::message>
# 🎉 Welcome to BPPHP Fun, {{ $user->name ?? '$user->email' }}!

<div style="margin-top: 30px;">
    Thank you for your ticket purchase — your account has been <strong>successfully created</strong>!
</div>


---
### 🔐 Your Login Credentials
- **Email:** {{ $user->email??'' }}
- **Password:** {{ $password??'' }}

Please log in and consider updating your password for security.

<x-mail::button :url="url('/login')">
    Login to Your Account
</x-mail::button>

---

We're excited to have you join the **BPPHP Movement**.
If you have any questions, feel free to reach out.

Thanks,
{{ config('app.name') }}
</x-mail::message>
