<!-- resources/views/tickets/index.blade.php -->
@extends('layouts.master')

@section('title', 'My Tickets')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">My Purchased Tickets</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($tickets->isEmpty())
                            <p class="text-center text-muted py-4">You haven't purchased any tickets yet. <a href="{{ route('events.index') }}">Browse events</a> to get started!</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ticket Code</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Purchase Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Joy Points Earned</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $ticket->event->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $ticket->event->event_date ? $ticket->event->event_date->format('M d, Y') : 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $ticket->ticket_code }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $ticket->purchase_date->format('M d, Y H:i A') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $ticket->is_redeemed ? 'secondary' : 'success' }}">
                                        {{ $ticket->is_redeemed ? 'Redeemed' : 'Active' }}
                                    </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($ticket->joy_points_earned, 2) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Ticket">
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
