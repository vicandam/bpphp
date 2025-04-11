@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success text-white mx-3 " role="alert" style="margin-bottom: 50px!important;">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Profile Information</h6>
                        <small class="text-capitalize ps-3">Update your account's profile information and email address.</small>
                    </div>

                    <div class="p-4">
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="input-group input-group-static mb-4">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Email</label>
                                <input type="text" class="form-control" name="email" value="{{old('email', $user->email) }}">
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">Save</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if (session('status') === 'password-updated')
                <div class="alert alert-success text-white mx-3 " role="alert" style="margin-bottom: 50px!important;">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Update Password</h6>
                        <small class="text-capitalize ps-3">Ensure your account is using a long, random password to stay secure.</small>
                    </div>

                    <div class="p-4">
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="input-group input-group-static mb-4">
                                <label>Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger" />
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>New Password</label>
                                <input type="password" class="form-control" name="password">
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation">
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">Save</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if (session('status') === 'settings-updated')
                <div class="alert alert-success text-white mx-3 " role="alert" style="margin-bottom: 50px!important;">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Connect Your Virtulab Account</h6>
                        <small class="text-capitalize ps-3">Provide your Virtulab API Key and Location ID to manage your contacts directly from this app.</small>
                    </div>

                    <div class="p-4">
                        <form method="post" action="{{ route('ghl.settings.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="input-group input-group-static mb-4">
                                <label>API Key *</label>
                                <input type="text" class="form-control" name="ghl_api_key" value="{{ old('ghl_api_key', $user->ghl_api_key) }}" required>
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Virtulab Location ID</label>
                                <input type="text" class="form-control" name="ghl_location_id" value="{{old('ghl_location_id', $user->ghl_location_id) }}">
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">Save</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
