<!-- resources/views/sponsors/edit.blade.php -->
@extends('layouts.master')

@section('title', 'Edit Sponsor')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Sponsor: {{ $sponsor->name }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('sponsors.update', $sponsor) }}" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Sponsor Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $sponsor->name) }}" required>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control" name="contact_person" value="{{ old('contact_person', $sponsor->contact_person) }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" name="contact_email" value="{{ old('contact_email', $sponsor->contact_email) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $sponsor->contact_phone) }}">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Sponsorship Type</label>
                            <input type="text" class="form-control" name="sponsorship_type" value="{{ old('sponsorship_type', $sponsor->sponsorship_type) }}" placeholder="e.g., Event Sponsorship, Film Sponsorship">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Amount Pledged (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="amount_pledged" value="{{ old('amount_pledged', $sponsor->amount_pledged) }}">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Referred By (Marketing Agent/Catalyst)</label>
                            <select class="form-control" name="referred_by_user_id">
                                <option value="">-- Select User (Optional) --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('referred_by_user_id', $sponsor->referred_by_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('sponsors.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Sponsor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
