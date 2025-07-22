<!-- resources/views/tickets/show.blade.php -->
@extends('layouts.master')

@section('title', 'Ticket Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">Ticket Details #{{ $ticket->id }}</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            {{-- Admin/Staff can redeem a ticket --}}
                            @if(!$ticket->is_redeemed)
                                <form action="{{ route('tickets.redeem', $ticket) }}" method="POST" class="d-inline me-3">
                                    @csrf
                                    <button type="submit" class="btn btn-white btn-sm mb-0" onclick="return confirm('Are you sure you want to mark this ticket as redeemed?')">Mark as Redeemed</button>
                                </form>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary me-3">Already Redeemed</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Event Name</label>
                            <p class="text-muted">{{ $ticket->event->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ticket Code</label>
                            <p class="text-muted text-lg font-weight-bold">{{ $ticket->ticket_code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purchased By</label>
                            <p class="text-muted">
                                @if($ticket->user)
                                    <a href="{{ route('users.show', $ticket->user) }}">{{ $ticket->user->name }} (ID: {{ $ticket->user->id }})</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purchase Date</label>
                            <p class="text-muted">{{ $ticket->purchase_date->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <p class="text-muted">
                            <span class="badge badge-sm bg-gradient-{{ $ticket->is_redeemed ? 'secondary' : 'success' }}">
                                {{ $ticket->is_redeemed ? 'Redeemed' : 'Active' }}
                            </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Joy Points Earned</label>
                            <p class="text-muted">{{ number_format($ticket->joy_points_earned, 2) }}</p>
                        </div>
                        {{-- Assuming QR code path is stored and accessible --}}
                        @if(isset($ticket->qr_code_path) && $ticket->qr_code_path)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">QR Code</label>
                                <div>
                                    <img src="{{ asset($ticket->qr_code_path) }}" alt="Ticket QR Code" style="max-width: 200px;">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary me-2">Back to My Tickets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
