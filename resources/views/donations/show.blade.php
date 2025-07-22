<!-- resources/views/donations/show.blade.php -->
@extends('layouts.master')

@section('title', 'Donation Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Donation Details #{{ $donation->id }}</h6>
                        <a href="{{ route('donations.edit', $donation) }}" class="btn btn-white btn-sm mb-0 me-3">Edit Donation</a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Donor Name</label>
                            <p class="text-muted">{{ $donation->donor_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Associated User</label>
                            <p class="text-muted">
                                @if($donation->user)
                                    <a href="{{ route('users.show', $donation->user) }}">{{ $donation->user->name }} (ID: {{ $donation->user->id }})</a>
                                @else
                                    Not a registered user
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Donation Type</label>
                            <p class="text-muted">{{ $donation->donation_type }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (if Cash)</label>
                            <p class="text-muted">
                                @if($donation->donation_type == 'Cash')
                                    ₱{{ number_format($donation->amount, 2) }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description (if In Kind)</label>
                            <p class="text-muted">{{ $donation->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Donation Date</label>
                            <p class="text-muted">{{ $donation->donation_date->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Recorded At</label>
                            <p class="text-muted">{{ $donation->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $donation->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('donations.index') }}" class="btn btn-secondary me-2">Back to List</a>
                        <form action="{{ route('donations.destroy', $donation) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this donation record?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
