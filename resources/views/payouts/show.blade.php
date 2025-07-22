<!-- resources/views/payouts/show.blade.php -->
@extends('layouts.master')

@section('title', 'Payout Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Payout Details #{{ $payout->id }}</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('payouts.edit', $payout) }}" class="btn btn-white btn-sm mb-0 me-3">Edit Payout</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User</label>
                            <p class="text-muted">
                                @if($payout->user)
                                    <a href="{{ route('users.show', $payout->user) }}">{{ $payout->user->name }} (ID: {{ $payout->user->id }})</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payout Type</label>
                            <p class="text-muted">{{ $payout->type }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount</label>
                            <p class="text-muted">₱{{ number_format($payout->amount, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <p class="text-muted">
                            <span class="badge badge-sm bg-gradient-{{ $payout->status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($payout->status) }}
                            </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Transaction Date</label>
                            <p class="text-muted">{{ $payout->transaction_date->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="text-muted">{{ $payout->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $payout->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('payouts.index') }}" class="btn btn-secondary me-2">Back to List</a>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <form action="{{ route('payouts.destroy', $payout) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this payout record?')">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
