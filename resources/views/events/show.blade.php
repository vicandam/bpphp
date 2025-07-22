<!-- resources/views/events/show.blade.php -->
@extends('layouts.master')

@section('title', $event->name . ' Details')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3">{{ $event->name }}</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('events.edit', $event) }}" class="btn btn-white btn-sm mb-0 me-3">Edit Event</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4 py-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <p class="text-muted">{{ $event->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <p class="text-muted">{{ $event->event_date ? $event->event_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time</label>
                            <p class="text-muted">{{ $event->event_time ? $event->event_time->format('h:i A') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Venue</label>
                            <p class="text-muted">{{ $event->venue ?? 'Online/TBD' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ticket Price</label>
                            <p class="text-muted">₱{{ number_format($event->ticket_price, 2) }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Event Types</label>
                            <p class="text-muted">
                                @if($event->is_movie_screening) <span class="badge badge-sm bg-gradient-info">Movie Screening</span> @endif
                                @if($event->is_concert) <span class="badge badge-sm bg-gradient-success">Concert</span> @endif
                                @if($event->is_seminar_workshop) <span class="badge badge-sm bg-gradient-warning">Seminar/Workshop</span> @endif
                                @if($event->is_other_event) <span class="badge badge-sm bg-gradient-secondary">Other Event</span> @endif
                                @if(!$event->is_movie_screening && !$event->is_concert && !$event->is_seminar_workshop && !$event->is_other_event)
                                    <span class="badge badge-sm bg-gradient-light text-dark">General Event</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end px-4 pb-2">
                        <a href="{{ route('events.index') }}" class="btn btn-secondary me-2">Back to Events</a>
                        @auth
                            <form action="{{ route('tickets.store') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <button type="submit" class="btn bg-gradient-primary" onclick="return confirm('Are you sure you want to purchase a ticket for this event for ₱{{ number_format($event->ticket_price, 2) }}?')">Buy Ticket</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn bg-gradient-primary">Login to Buy Ticket</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
