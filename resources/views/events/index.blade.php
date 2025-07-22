<!-- resources/views/events/index.blade.php -->
@extends('layouts.master')

@section('title', 'Events')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Upcoming Events</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('events.create') }}" class="btn btn-white btn-sm mb-0 me-3">Add New Event</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($events->isEmpty())
                            <p class="text-center text-muted py-4">No events found at the moment. Please check back later!</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date & Time</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Venue</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ticket Price</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($events as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">
                                                        @if($event->is_movie_screening) movie
                                                        @elseif($event->is_concert) music_note
                                                        @elseif($event->is_seminar_workshop) school
                                                        @else event
                                                        @endif
                                                    </i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $event->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ Str::limit($event->description, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $event->event_date ? $event->event_date->format('M d, Y') : 'N/A' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $event->event_time ? $event->event_time->format('h:i A') : '' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $event->venue ?? 'Online/TBD' }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($event->ticket_price, 2) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('events.show', $event) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Event">
                                                View
                                            </a>
                                            @if(Auth::user() && Auth::user()->is_admin)
                                                <a href="{{ route('events.edit', $event) }}" class="text-secondary font-weight-bold text-xs ms-3" data-toggle="tooltip" data-original-title="Edit Event">
                                                    Edit
                                                </a>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
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
