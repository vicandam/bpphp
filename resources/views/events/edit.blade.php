<!-- resources/views/events/edit.blade.php -->
@extends('layouts.master')

@section('title', 'Edit Event')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Event: {{ $event->name }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('events.update', $event) }}" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Event Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $event->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Ticket Price</label>
                                    <input type="number" step="0.01" class="form-control" name="ticket_price" value="{{ old('ticket_price', $event->ticket_price) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Event Date</label>
                                    <input type="date" class="form-control" name="event_date" value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Event Time</label>
                                    <input type="time" class="form-control" name="event_time" value="{{ old('event_time', $event->event_time ? $event->event_time->format('H:i') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" name="venue" value="{{ old('venue', $event->venue) }}">
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="form-check form-switch d-flex align-items-center mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_movie_screening" name="is_movie_screening" value="1" {{ old('is_movie_screening', $event->is_movie_screening) ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0 ms-3" for="is_movie_screening">Movie Screening</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch d-flex align-items-center mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_concert" name="is_concert" value="1" {{ old('is_concert', $event->is_concert) ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0 ms-3" for="is_concert">Concert</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch d-flex align-items-center mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_seminar_workshop" name="is_seminar_workshop" value="1" {{ old('is_seminar_workshop', $event->is_seminar_workshop) ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0 ms-3" for="is_seminar_workshop">Seminar/Workshop</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch d-flex align-items-center mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_other_event" name="is_other_event" value="1" {{ old('is_other_event', $event->is_other_event) ? 'checked' : '' }}>
                                    <label class="form-check-label mb-0 ms-3" for="is_other_event">Other Event</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
