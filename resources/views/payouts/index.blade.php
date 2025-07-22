<!-- resources/views/payouts/index.blade.php -->
@extends('layouts.master')

@section('title', 'All Payouts')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Payouts List</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('payouts.create') }}" class="btn btn-white btn-sm mb-0 me-3">Create New Payout</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($payouts->isEmpty())
                            <p class="text-center text-muted py-4">No payouts recorded yet.</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Transaction Date</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($payouts as $payout)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">person</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $payout->user->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">ID: {{ $payout->user_id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $payout->type }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($payout->amount, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $payout->status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $payout->transaction_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('payouts.show', $payout) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Payout">
                                                View
                                            </a>
                                            @if(Auth::user() && Auth::user()->is_admin)
                                                <a href="{{ route('payouts.edit', $payout) }}" class="text-secondary font-weight-bold text-xs ms-3" data-toggle="tooltip" data-original-title="Edit Payout">
                                                    Edit
                                                </a>
                                                <form action="{{ route('payouts.destroy', $payout) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this payout record?')">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
