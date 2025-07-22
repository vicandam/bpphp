<!-- resources/views/membership_types/show.blade.php -->
@extends('layouts.master')

@section('title', $membershipType->name . ' Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Membership Type: {{ $membershipType->name }}</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('membership-types.edit', $membershipType) }}" class="btn btn-white btn-sm mb-0 me-3">Edit Membership Type</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Name</label>
                            <p class="text-muted">{{ $membershipType->name }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <p class="text-muted">{{ $membershipType->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Members</label>
                            <p class="text-muted">{{ $membershipType->users->count() }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="text-muted">{{ $membershipType->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $membershipType->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('membership-types.index') }}" class="btn btn-secondary me-2">Back to List</a>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <form action="{{ route('membership-types.destroy', $membershipType) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this membership type? This will also affect users associated with it.')">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
