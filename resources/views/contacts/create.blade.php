@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Create Contact</h6>
                    </div>

                    <div class="p-4">
                        <form action="{{ route('contacts.store') }}" method="POST">
                            @csrf

                            <div class="input-group input-group-static mb-4">
                                <label>First Name *</label>
                                <input type="text" class="form-control" name="firstName" required>
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lastName">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Contact</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
