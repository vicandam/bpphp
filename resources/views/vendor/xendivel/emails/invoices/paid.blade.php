<x-mail::message>
# Greetings!

<p>
@if ($customMessage === null)
Thank you for your recent purchase from {{ config('app.name') }}. We have attached your invoice to this email.
@else
{{ $customMessage }}
@endif
</p>

{{-- Only show if QR exists --}}
@if($ticket->virtual_membership_card_qr)
<table width="100%" style="text-align: center; margin-top: 30px;">
<tr>
<td>
<strong style="display: block; margin-bottom: 8px;">🎟️ Your Event Ticket QR Code</strong>
<img src="{{ asset($ticket->virtual_membership_card_qr) }}" alt="Ticket QR Code" width="200" style="border: 1px solid #ddd; padding: 5px;">
<p style="margin-top: 8px; font-size: 14px; color: #555; text-align: center;">Show this at the venue entrance to gain access.</p>

{{-- Download link --}}
<a href="{{ asset($ticket->virtual_membership_card_qr) }}" download style="display: inline-block; margin-top: 10px; background-color: #2d3748; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
⬇️ Download QR Code
</a>
</td>
</tr>
</table>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
