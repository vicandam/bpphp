<!-- resources/views/investments/show.blade.php -->
@extends('layouts.master')

@section('title', 'Investment Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Investment Details #{{ $investment->id }}</h6>
                        {{-- Edit button for investments is not typically exposed to users,
                             as investments are usually immutable once made.
                             If admin editing is desired, add it here. --}}
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Investor</label>
                            <p class="text-muted">
                                @if($investment->user)
                                    <a href="{{ route('users.show', $investment->user) }}">{{ $investment->user->name }} (ID: {{ $investment->user->id }})</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Investment Amount</label>
                            <p class="text-muted">₱{{ number_format($investment->investment_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Number of Shares</label>
                            <p class="text-muted">{{ $investment->number_of_shares }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Investment Date</label>
                            <p class="text-muted">{{ $investment->investment_date->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Associated Film Project</label>
                            <p class="text-muted">
                                @if($investment->filmProject)
                                    <a href="{{ route('film_projects.show', $investment->filmProject) }}">{{ $investment->filmProject->title }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Associated Event</label>
                            <p class="text-muted">
                                @if($investment->event)
                                    <a href="{{ route('events.show', $investment->event) }}">{{ $investment->event->name }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Referred By</label>
                            <p class="text-muted">
                                @if($investment->referredBy)
                                    <a href="{{ route('users.show', $investment->referredBy) }}">{{ $investment->referredBy->name }} (ID: {{ $investment->referredBy->id }})</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recorded At</label>
                            <p class="text-muted">{{ $investment->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('investments.index') }}" class="btn btn-secondary me-2">Back to My Investments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
