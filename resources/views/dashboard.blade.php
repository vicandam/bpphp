<!-- resources/views/dashboard.blade.php -->
@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            <p class="mb-4">
                Monitor overall BPP Wallet balance, total BPP Points redeemed, Referrals and Tickets Purchased.
            </p>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">My BPP Wallet</p>
                            <h4 class="mb-0">₱{{ number_format(Auth::user()->bpp_wallet_balance, 2) }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">wallet</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+₱100 </span>from new referrals</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">My BPP Points</p>
                            <h4 class="mb-0">{{ number_format(Auth::user()->bpp_points_balance, 2) }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">star</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+1 </span>for every ₱200 ticket purchase</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">My Referrals</p>
                            <h4 class="mb-0">{{ Auth::user()->madeReferrals->count() }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">group</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm">Total members referred</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">My Tickets</p>
                            <h4 class="mb-0">{{ Auth::user()->tickets->count() }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">movie_filter</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm">Total tickets purchased</p>
                </div>
            </div>
        </div>
    </div>



    <div class="row mt-5">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2 ">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Upcoming Events</h6>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm">
                        Keep an eye on exciting events!
                    </p>
                    {{-- You can display a list of upcoming events here --}}
                    <a href="{{ route('public.events.index') }}" class="btn btn-sm bg-gradient-primary mt-3">View All Events</a>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-info shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Latest Film Projects</h6>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-sm">
                        Discover new opportunities to invest in dreams.
                    </p>
                    {{-- You can display a list of latest film projects here --}}
                    <a href="{{ route('film_projects.index') }}" class="btn btn-sm bg-gradient-info mt-3">View All Film Projects</a>
                </div>
            </div>
        </div>
    </div>
@endsection
