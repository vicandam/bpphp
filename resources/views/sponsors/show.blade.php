<!-- resources/views/sponsors/show.blade.php -->
@extends('layouts.master')

@section('title', $sponsor->name . ' Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Sponsor Details: {{ $sponsor->name }}</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('sponsors.edit', $sponsor) }}" class="btn btn-white btn-sm mb-0 me-3">Edit Sponsor</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sponsor Name</label>
                            <p class="text-muted">{{ $sponsor->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <p class="text-muted">{{ $sponsor->contact_person ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <p class="text-muted">{{ $sponsor->contact_email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <p class="text-muted">{{ $sponsor->contact_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sponsorship Type</label>
                            <p class="text-muted">{{ $sponsor->sponsorship_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount Pledged</label>
                            <p class="text-muted">₱{{ number_format($sponsor->amount_pledged, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Referred By</label>
                            <p class="text-muted">
                                @if($sponsor->referredBy)
                                    <a href="{{ route('users.show', $sponsor->referredBy) }}">{{ $sponsor->referredBy->name }} (ID: {{ $sponsor->referredBy->id }})</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="text-muted">{{ $sponsor->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $sponsor->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('sponsors.index') }}" class="btn btn-secondary me-2">Back to List</a>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this sponsor record?')">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
