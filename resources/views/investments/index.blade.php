<!-- resources/views/investments/index.blade.php -->
@extends('layouts.master')

@section('title', 'My Investments')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">My Investments</h6>
                        <a href="{{ route('investments.create') }}" class="btn btn-white btn-sm mb-0 me-3">Make New Investment</a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($investments->isEmpty())
                            <p class="text-center text-muted py-4">You have not made any investments yet. <a href="{{ route('investments.create') }}">Start investing</a> in our projects and events!</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Investment Details</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Shares</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referred By</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($investments as $investment)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">
                                                        @if($investment->filmProject) movie
                                                        @elseif($investment->event) event
                                                        @else trending_up
                                                        @endif
                                                    </i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">₱{{ number_format($investment->investment_amount, 2) }}</h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        @if($investment->filmProject)
                                                            For: {{ $investment->filmProject->title }} (Film Project)
                                                        @elseif($investment->event)
                                                            For: {{ $investment->event->name }} (Event)
                                                        @else
                                                            General Investment
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $investment->number_of_shares }} shares</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $investment->investment_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{ $investment->referredBy->name ?? 'N/A' }}
                                    </span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('investments.show', $investment) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Investment">
                                                View Details
                                            </a>
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
