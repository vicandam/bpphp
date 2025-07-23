<!-- resources/views/investments/create.blade.php -->
@extends('layouts.master')

@section('title', 'Make an Investment')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">New Investment</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('investments.store') }}" class="p-4">
                        @csrf
                        <p class="text-muted mb-4">Minimum investment amount is ₱10,000 for 1 share.</p>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Investment Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="investment_amount" value="{{ old('investment_amount') }}" required min="10000">
                        </div>

                        <div class="form-group my-3">
                            <label class="form-label">Select Film Project (Optional)</label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="film_project_id" id="film_project_id">
                                    <option value="">-- Select Film Project --</option>
                                    @foreach($filmProjects as $project)
                                        <option value="{{ $project->id }}" {{ old('film_project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group my-3">
                            <label class="form-label">Select Event (Optional)</label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="event_id" id="event_id">
                                    <option value="">-- Select Event --</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->event_date->format('M d, Y') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <p class="text-muted text-sm mt-n2 mb-3">Please select either a Film Project OR an Event, not both.</p>

                        <div class="form-group my-3">
                            <label class="form-label">Referred By (Optional)</label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="referred_by_user_id" id="referred_by_user_id">
                                    <option value="">-- Select Referrer (Marketing Agent/Catalyst) --</option>
                                    @foreach($referrers as $referrer)
                                        <option value="{{ $referrer->id }}" {{ old('referred_by_user_id') == $referrer->id ? 'selected' : '' }}>{{ $referrer->name }} (ID: {{ $referrer->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Investment Source</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="source" id="source_new_money" value="new_money" {{ old('source') == 'new_money' || !old('source') ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="source_new_money">New Money (via Payment Gateway)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="source" id="source_bpp_wallet" value="bpp_wallet" {{ old('source') == 'bpp_wallet' ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="source_bpp_wallet">Use BPP Wallet (Current Balance: ₱{{ number_format(Auth::user()->bpp_wallet_balance, 2) }})</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('investments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Submit Investment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
