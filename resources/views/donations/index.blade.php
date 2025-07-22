<!-- resources/views/donations/index.blade.php -->
@extends('layouts.master')

@section('title', 'All Donations')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Donations Received</h6>
                        {{-- Admin can create a donation manually if needed --}}
                        <a href="{{ route('donations.create') }}" class="btn btn-white btn-sm mb-0 me-3">Record New Donation</a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($donations->isEmpty())
                            <p class="text-center text-muted py-4">No donations recorded yet.</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Donor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount/Description</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($donations as $donation)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">volunteer_activism</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $donation->user->name ?? $donation->donor_name ?? 'Anonymous' }}</h6>
                                                    @if($donation->user)
                                                        <p class="text-xs text-secondary mb-0">Registered User (ID: {{ $donation->user->id }})</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $donation->donation_type }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if($donation->donation_type == 'Cash')
                                                <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($donation->amount, 2) }}</span>
                                            @else
                                                <span class="text-secondary text-xs font-weight-bold">{{ Str::limit($donation->description, 50) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $donation->donation_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('donations.show', $donation) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Donation">
                                                View
                                            </a>
                                            <a href="{{ route('donations.edit', $donation) }}" class="text-secondary font-weight-bold text-xs ms-3" data-toggle="tooltip" data-original-title="Edit Donation">
                                                Edit
                                            </a>
                                            <form action="{{ route('donations.destroy', $donation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this donation record?')">Delete</button>
                                            </form>
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
