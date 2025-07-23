<!-- resources/views/donations/edit.blade.php -->
@extends('layouts.master')

@section('title', 'Edit Donation')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Donation #{{ $donation->id }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('donations.update', $donation) }}" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="input-group input-group-static is-filled my-3">
                            <label class="form-label">Donor Name</label>
                            <input type="text" class="form-control" name="donor_name" value="{{ old('donor_name', $donation->donor_name) }}">
                        </div>

                        <div class="input-group input-group-static is-filled my-3">
                            <label class="form-label">Associated User (Optional)</label>
                            <select class="form-control" name="user_id">
                                <option value="">-- Select Registered User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $donation->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group my-3">
                            <label class="form-label">Donation Type</label>
                            <div class="input-group input-group-static is-filled">
                                <select class="form-control" name="donation_type" id="donation_type" required>
                                    <option value="Cash" {{ old('donation_type', $donation->donation_type) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="In Kind (Products/Services)" {{ old('donation_type', $donation->donation_type) == 'In Kind (Products/Services)' ? 'selected' : '' }}>In Kind (Products/Services)</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group input-group-static is-filled my-3" id="amount_field" style="{{ old('donation_type', $donation->donation_type) == 'Cash' ? '' : 'display:none;' }}">
                            <label class="form-label">Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="{{ old('amount', $donation->amount) }}">
                        </div>

                        <div class="input-group input-group-static is-filled my-3" id="description_field" style="{{ old('donation_type', $donation->donation_type) == 'In Kind (Products/Services)' ? '' : 'display:none;' }}">
                            <label class="form-label">Description (for In Kind)</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description', $donation->description) }}</textarea>
                        </div>

                        <div class="input-group input-group-static is-filled my-3">
                            <label class="form-label">Donation Date</label>
                            <input type="date" class="form-control" name="donation_date" value="{{ old('donation_date', $donation->donation_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('donations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Donation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const donationTypeSelect = document.getElementById('donation_type');
            const amountField = document.getElementById('amount_field');
            const descriptionField = document.getElementById('description_field');

            descriptionField.style.display = 'none';
            amountField.style.display = 'none';

            function toggleFields() {
                if (donationTypeSelect.value === 'Cash') {
                    amountField.style.display = 'block';
                    descriptionField.style.display = 'none';

                    const amountInput = amountField.querySelector('input');
                    amountInput.setAttribute('required', 'required');
                    descriptionField.querySelector('textarea').removeAttribute('required');
                } else {
                    amountField.style.display = 'none';
                    descriptionField.style.display = 'block';

                    const descriptionTextarea = descriptionField.querySelector('textarea');
                    amountField.querySelector('input').removeAttribute('required');
                    descriptionTextarea.setAttribute('required', 'required');
                }
            }


            donationTypeSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
@endpush
