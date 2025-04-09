@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Contact</h6>
                    </div>

                    <div class="p-4">
                        <form method="POST" action="{{ route('contacts.update', $contact['id']) }}" >
                            @csrf
                            @method('PUT')
                            <div class="input-group input-group-static mb-4">
                                <label>First Name</label>
                                <input type="text" name="firstName" value="{{ old('firstName', $contact['firstName']) }}" class="form-control">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="lastName" value="{{ old('lastName', $contact['lastName']) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email', $contact['email']) }}" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-secondary btn-lg">Save</button>
                            <a href="{{route('dashboard')}}" class="btn btn-danger btn-lg">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
