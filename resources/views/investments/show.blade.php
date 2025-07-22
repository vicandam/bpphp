<!-- resources/views/investments/edit.blade.php -->
@extends('layouts.master')

@section('title', 'Edit Investment')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Investment #{{ $investment->id }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('investments.update', $investment) }}" class="p-4">
                        @csrf
                        @method('PUT')

                        <p class="text-muted mb-4">You are editing an existing investment. Note that changing the amount will recalculate shares.</p>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Investment Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="investment_amount" value="{{ old('investment_amount', $investment->investment_amount) }}" required min="10000">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Select Film Project (Optional)</label>
                            <select class="form-control" name="film_project_id" id="film_project_id">
                                <option value="">-- Select Film Project --</option>
                                @foreach($filmProjects as $project)
                                    <option value="{{ $project->id }}" {{ old('film_project_id', $investment->film_project_id) == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Select Event (Optional)</label>
                            <select class="form-control" name="event_id" id="event_id">
                                <option value="">-- Select Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id', $investment->event_id) == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->event_date ? $event->event_date->format('M d, Y') : 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-muted text-sm mt-n2 mb-3">Please select either a Film Project OR an Event, not both.</p>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Referred By (Optional)</label>
                            <select class="form-control" name="referred_by_user_id" id="referred_by_user_id">
                                <option value="">-- Select Referrer (Marketing Agent/Catalyst) --</option>
                                @foreach($referrers as $referrer)
                                    <option value="{{ $referrer->id }}" {{ old('referred_by_user_id', $investment->referred_by_user_id) == $referrer->id ? 'selected' : '' }}>{{ $referrer->name }} (ID: {{ $referrer->id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('investments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Investment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
