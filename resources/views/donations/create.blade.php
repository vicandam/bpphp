<!-- resources/views/donations/create.blade.php -->
@extends('layouts.master')

@section('title', 'Make a Donation')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Make a Donation</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('donations.store') }}" class="p-4">
                        @csrf

                        @guest
                            <p class="text-muted mb-4">You are not logged in. Please provide your name to make a donation. If you have an account, you can <a href="{{ route('login') }}">login here</a>.</p>
                            <div class="input-group input-group-outline my-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" class="form-control" name="donor_name" value="{{ old('donor_name') }}" required>
                            </div>
                        @else
                            <p class="text-muted mb-4">You are logged in as <strong>{{ Auth::user()->name }}</strong>. Your donation will be associated with your account.</p>
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                            <div class="input-group input-group-static my-3">
                                <label class="form-label">Donor Name</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                            </div>
                        @endguest

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Donation Type</label>
                            <select class="form-control" name="donation_type" id="donation_type" required>
                                <option value="Cash" {{ old('donation_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="In Kind (Products/Services)" {{ old('donation_type') == 'In Kind (Products/Services)' ? 'selected' : '' }}>In Kind (Products/Services)</option>
                            </select>
                        </div>

                        <div class="input-group input-group-outline my-3" id="amount_field" style="{{ old('donation_type') == 'Cash' || !old('donation_type') ? '' : 'display:none;' }}">
                            <label class="form-label">Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="{{ old('amount') }}">
                        </div>

                        <div class="input-group input-group-outline my-3" id="description_field" style="{{ old('donation_type') == 'In Kind (Products/Services)' ? '' : 'display:none;' }}">
                            <label class="form-label">Description (for In Kind)</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Submit Donation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const donationTypeSelect = document.getElementById('donation_type');
            const amountField = document.getElementById('amount_field');
            const descriptionField = document.getElementById('description_field');

            function toggleFields() {
                if (donationTypeSelect.value === 'Cash') {
                    amountField.style.display = 'block';
                    descriptionField.style.display = 'none';
                    amountField.querySelector('input').setAttribute('required', 'required');
                    descriptionField.querySelector('textarea').removeAttribute('required');
                } else {
                    amountField.style.display = 'none';
                    descriptionField.style.display = 'block';
                    amountField.querySelector('input').removeAttribute('required');
                    descriptionField.querySelector('textarea').setAttribute('required', 'required');
                }
            }

            donationTypeSelect.addEventListener('change', toggleFields);
            toggleFields(); // Call on initial load to set correct visibility
        });
    </script>
@endsection
