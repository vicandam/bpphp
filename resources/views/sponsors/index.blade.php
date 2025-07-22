<!-- resources/views/sponsors/index.blade.php -->
@extends('layouts.master')

@section('title', 'Sponsors')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Our Sponsors</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('sponsors.create') }}" class="btn btn-white btn-sm mb-0 me-3">Add New Sponsor</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($sponsors->isEmpty())
                            <p class="text-center text-muted py-4">No sponsors found yet.</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sponsor Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contact Person</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sponsorship Type</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount Pledged</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referred By</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sponsors as $sponsor)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">business</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $sponsor->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $sponsor->contact_email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $sponsor->contact_person ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $sponsor->sponsorship_type ?? 'General' }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($sponsor->amount_pledged, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $sponsor->referredBy->name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('sponsors.show', $sponsor) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Sponsor">
                                                View
                                            </a>
                                            @if(Auth::user() && Auth::user()->is_admin)
                                                <a href="{{ route('sponsors.edit', $sponsor) }}" class="text-secondary font-weight-bold text-xs ms-3" data-toggle="tooltip" data-original-title="Edit Sponsor">
                                                    Edit
                                                </a>
                                                <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this sponsor?')">Delete</button>
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
