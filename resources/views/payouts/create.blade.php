<!-- resources/views/payouts/create.blade.php -->
@extends('layouts.master')

@section('title', 'Create New Payout')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Create New Payout</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('payouts.store') }}" class="p-4">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">User</label>
                            <select class="form-control" name="user_id" required>
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Payout Type</label>
                            <input type="text" class="form-control" name="type" value="{{ old('type') }}" required placeholder="e.g., Referral Rewards, Angel Investor Share">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="{{ old('amount') }}" required min="0">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('payouts.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Create Payout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
