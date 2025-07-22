<!-- resources/views/membership_types/create.blade.php -->
@extends('layouts.master')

@section('title', 'Create New Membership Type')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Create New Membership Type</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('membership-types.store') }}" class="p-4">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Membership Type Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('membership-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Create Membership Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
