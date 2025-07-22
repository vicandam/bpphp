<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BusinessPartnerController;
use App\Http\Controllers\CustomPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FilmProjectController;
use App\Http\Controllers\GHLContactController;
use App\Http\Controllers\GHLSettingsController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\PartnerProductServiceController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicFilmProjectController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $contacts = [];
    $meta = [];

    return view('dashboard', compact('contacts', 'meta'));

})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/ghl-settings', [GHLSettingsController::class, 'update'])->name('ghl.settings.update');

    Route::get('/contacts/{id}/edit', [GHLContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{id}', [GHLContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{id}', [GHLContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/contacts/create', [DashboardController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [GHLContactController::class, 'store'])->name('contacts.store');

    Route::get('/account', [DashboardController::class, 'account'])->name('account');

    // Custom User Profile Routes (beyond basic Laravel profile)
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});

// Overriding Laravel's default registration to include custom fields and referral logic
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');


// Publicly accessible routes for events and film projects
Route::get('/public/events', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/public/events/{event}', [PublicEventController::class, 'show'])->name('public.events.show');

Route::get('/film-projects', [PublicFilmProjectController::class, 'index'])->name('public.film_projects.index');
Route::get('/film-projects/{filmProject}', [PublicFilmProjectController::class, 'show'])->name('public.film_projects.show');

// Publicly accessible for viewing business partners and their products/services
Route::get('/business-partners', [BusinessPartnerController::class, 'index'])->name('business_partners.index');
Route::get('/business-partners/{businessPartner}', [BusinessPartnerController::class, 'show'])->name('business_partners.show');
Route::get('/business-partners/{businessPartner}/products-services', [PartnerProductServiceController::class, 'index'])->name('business_partners.products_services.index');
Route::get('/business-partners/{businessPartner}/products-services/{partnerProductService}', [PartnerProductServiceController::class, 'show'])->name('business_partners.products_services.show');
Route::post('/products-services/{partnerProductService}/redeem', [PartnerProductServiceController::class, 'redeem'])->middleware('auth')->name('products_services.redeem');


// Authenticated user routes
Route::middleware('auth')->group(function () {
    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Investments
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
    Route::get('/investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');

    // Referrals (user's own referrals)
    Route::get('/my-referrals', [ReferralController::class, 'index'])->name('referrals.my');

    // Donations (user can create)
    Route::get('/donations/create', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
});


// Admin-only routes (requires 'admin' middleware)
// You need to define the 'admin' middleware in app/Http/Kernel.php
// and implement its logic (e.g., checking if Auth::user()->is_admin is true)
Route::middleware(['auth', 'admin'])->group(function () {
    // Membership Types
    Route::resource('membership-types', MembershipTypeController::class);

    // Events (Admin CRUD)
    Route::get('/admin/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/admin/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/admin/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/admin/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/admin/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/admin/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/admin/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/tickets/{ticket}/redeem', [TicketController::class, 'redeem'])->name('tickets.redeem'); // Admin/Staff action

    // Business Partners (Admin/Marketing Agent CRUD)
    Route::get('/admin/business-partners/create', [BusinessPartnerController::class, 'create'])->name('business_partners.create');
    Route::post('/admin/business-partners', [BusinessPartnerController::class, 'store'])->name('business_partners.store');
    Route::get('/admin/business-partners/{businessPartner}/edit', [BusinessPartnerController::class, 'edit'])->name('business_partners.edit');
    Route::put('/admin/business-partners/{businessPartner}', [BusinessPartnerController::class, 'update'])->name('business_partners.update');
    Route::delete('/admin/business-partners/{businessPartner}', [BusinessPartnerController::class, 'destroy'])->name('business_partners.destroy');

    // Partner Products/Services (Admin CRUD)
    Route::get('/admin/business-partners/{businessPartner}/products-services/create', [PartnerProductServiceController::class, 'create'])->name('business_partners.products_services.create');
    Route::post('/admin/business-partners/{businessPartner}/products-services', [PartnerProductServiceController::class, 'store'])->name('business_partners.products_services.store');
    Route::get('/admin/business-partners/{businessPartner}/products-services/{partnerProductService}/edit', [PartnerProductServiceController::class, 'edit'])->name('business_partners.products_services.edit');
    Route::put('/admin/business-partners/{businessPartner}/products-services/{partnerProductService}', [PartnerProductServiceController::class, 'update'])->name('business_partners.products_services.update');
    Route::delete('/admin/business-partners/{businessPartner}/products-services/{partnerProductService}', [PartnerProductServiceController::class, 'destroy'])->name('business_partners.products_services.destroy');

    // Sponsors
    Route::resource('sponsors', SponsorController::class);

    // Film Projects (Admin CRUD)
    Route::get('/admin/film-projects/create', [FilmProjectController::class, 'create'])->name('film_projects.create');
    Route::get('/admin/film-projects', [FilmProjectController::class, 'index'])->name('film_projects.index');
    Route::get('/admin/film-projects/{filmProject}', [FilmProjectController::class, 'show'])->name('film_projects.show');
    Route::post('/admin/film-projects', [FilmProjectController::class, 'store'])->name('film_projects.store');
    Route::get('/admin/film-projects/{filmProject}/edit', [FilmProjectController::class, 'edit'])->name('film_projects.edit');
    Route::put('/admin/film-projects/{filmProject}', [FilmProjectController::class, 'update'])->name('film_projects.update');
    Route::delete('/admin/film-projects/{filmProject}', [FilmProjectController::class, 'destroy'])->name('film_projects.destroy');
    Route::post('/film-projects/{filmProject}/calculate-shares', [FilmProjectController::class, 'calculateAngelInvestorShares'])->name('film_projects.calculate_shares');

    // Payouts
    Route::resource('payouts', PayoutController::class);

    // Donations (Admin view/manage)
    Route::get('/admin/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/admin/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/admin/donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::put('/admin/donations/{donation}', [DonationController::class, 'update'])->name('donations.update');
    Route::delete('/admin/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');

    // User elevation actions (Admin triggered)
    Route::post('/users/{user}/elevate-marketing-agent', [UserController::class, 'elevateToMarketingAgent'])->name('users.elevate_marketing_agent');
    Route::post('/users/{user}/elevate-marketing-catalyst', [UserController::class, 'elevateToMarketingCatalyst'])->name('users.elevate_marketing_catalyst');
    Route::post('/users/{user}/elevate-angel-investor', [UserController::class, 'elevateToAngelInvestor'])->name('users.elevate_angel_investor');
    Route::post('/users/{user}/elevate-golden-hearts-awardee', [UserController::class, 'elevateToGoldenHeartsAwardee'])->name('users.elevate_golden_hearts_awardee');
});

require __DIR__.'/auth.php';
