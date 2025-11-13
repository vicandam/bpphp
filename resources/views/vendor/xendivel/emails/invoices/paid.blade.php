<x-mail::message>
# Hi {{ $first_name }}!

Thank you for your Alive@50 ticket purchase from Big Perspective Productions.
Welcome to our BPPHP Movement!

<div style="text-align: center; margin: 20px 0; font-size: 16px; line-height: 1.8; font-weight: bold; color: #2d3748;">
Be <br>
Positive-minded <br>
Productive and <br>
Happy <br>
Philippines 🎉
</div>

---

📄 Attached is your sales invoice.

📍 Below is your QR Code/Digital Ticket to **Alive@50** on **November 29, 2025, 5:00 PM**
at the **One Cainta Auditorium, Cainta Elementary School A. Bonifacio St. Caintan, Rizal.**

@if($ticket->virtual_membership_card_qr)
<table width="100%" style="text-align: center; margin-top: 30px;">
<tr>
<td>
<strong style="display: block; margin-bottom: 8px;">🎟️ QR Code for Alive@50</strong>
<img src="{{ asset($ticket->virtual_membership_card_qr) }}" alt="Ticket QR Code" width="200" style="border: 1px solid #ddd; padding: 5px;">
<p style="margin-top: 8px; font-size: 14px; color: #555;">Show this QR code at the venue entrance to gain access.</p>

{{-- Download link --}}
<a href="{{ asset($ticket->virtual_membership_card_qr) }}" download style="display: inline-block; margin-top: 10px; background-color: #2d3748; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
⬇️ Download QR Code
</a>
</td>
</tr>
</table>
@endif

---

{{-- Buyer Name + Referral Code --}}
@if(isset($referral_code))
<table width="100%" style="text-align: center; margin-top: 20px; background-color: #f9fafb; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
<tr>
<td>
<p style="font-size: 15px; color: #2d3748; margin: 0; font-weight: bold;">
{{ $name }}
</p>
<p style="font-size: 14px; color: #555; margin: 0;">
Member’s Referral Code: {{ $referral_code }}
</p>
</td>
</tr>
</table>
@endif

---

✨ Excited to see you at the event in your **gold or champagne** and **navy blue** semi-formal attire or business attire.

To our shared success and happiness, <br>
**Big Perspective Productions Team**
</x-mail::message>
