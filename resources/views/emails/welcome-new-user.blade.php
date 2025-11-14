<x-mail::message>
# Hi {{ $user->first_name ?? '$user->email' }}! 🎉

As a welcome gift for purchasing a ticket to **Alive@50**, you get a **FREE membership** to **Moviegoers and Musiclovers Dream Club (MMDCI).**

As a member, you will get these **opportunities and privileges valid for 2 years:**

1. ₱100 cash reward for every referred paid ticket buyer to events organized by Big Perspective Productions.
2. 15% cash reward for every referred fully paid event sponsor.
3. Members-only discounts soon and freebies from partner online businesses, physical stores, and seminars/workshops organized or supported by Big Perspective Productions (after referring at least one ticket buyer to our events). Watch out for announcements soon.
4. Members-only privilege to join and win in contests, raffles, and games on our website and during events.
5. Privilege to earn one (1) joy point for every P500 worth of event ticket from Big Perspective Productions. 1 joy point = ₱1. Points are not convertible to cash and can only be used to redeem tickets and items under the Business Partners Club on the website of bpphp.fun.
6. 10% share in the net theatrical sales of the film “Stitched Hearts” provided you have bought at least 20 event or movie tickets from BPPHP.FUN worth ₱500 each.
7. Elevation to the Lucky Marketing Agents and Event Catalyst Club after referring an event sponsor with at least ₱10,000 cash sponsorship.
8. Opportunity to earn unlimitedly from sales of your products and services under our Business Partners Club with our growing MMDCI members. (Business Partners Club Packages available upon request)
9. 30% income from the net theatrical ticket sales of a film nationwide and worldwide (as a signed-up Member of the Angel Investors Circle). Details upon request.
10. Privilege to be part of a solution towards a common vision — **A Poverty-free and Happy Philippines and World.**

---

All these benefits and opportunities are yours by simply promoting and sharing our photo and video ads on social media, and by referring your batchmates, friends, family members, relatives, and workmates to buy event tickets using your mobile phone with WiFi or data — from the comfort of your home or anywhere in the world. **Amazing, isn’t it?** 🎯

---

### 🔐 Your Member Account Login
- **Email:** {{ $user->email ?? '' }}
- **Temporary Password:** {{ $password ?? '' }}

Please log in and change your password to secure your account.

<x-mail::button :url="url('/login')">
    Login to Your Account
</x-mail::button>

---

### 📢 Connect With Our Community
Join our growing network and get updates by following us on Facebook:

- [Moviegoers and Musiclovers Dream Club](https://www.facebook.com/moviegoersdreamclub)
- [Big Perspective Productions](https://www.facebook.com/profile.php?id=100064230332048)

---

📩 For inquiries, contact us via:
- Messenger: **Big Perspective Productions**
- Viber: **0927 529 0731**
- Email: **support@bpphp.fun**

---

We are very excited to grow, help, and achieve more with you!

To our shared success and happiness, <br>
**Big Perspective Productions Family**
</x-mail::message>
