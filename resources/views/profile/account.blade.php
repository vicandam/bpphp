@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
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
                        <h6 class="text-xs ps-3 text-muted">Update your account's profile information and email address.</h6>
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

                            <div class="input-group input-group-static mb-4">
                                <label>Mobile</label>
                                <input type="text" class="form-control" name="mobile_no" value="{{old('mobile_no', $user->mobile_no) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Birthday</label>
{{--                                <input type="text" class="form-control" name="birthday" value="{{ old('birthday', $user->birthday) }}">--}}
                                <input type="text" id="birthday" name="birthday" class="form-control" value="{{ old('birthday', $user->birthday) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>City or Province</label>
                                <input type="text" class="form-control" name="city_or_province" value="{{ old('city_or_province', $user->city_or_province) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Country</label>
{{--                                <input type="text" class="form-control" name="country" value="{{ old('country', $user->country) }}">--}}

                                <select id="country" name="country" class="form-control select2">
                                    <option value="Philippines">Philippines</option>
                                    <option value="United States">United States</option>
                                    <!-- Add more -->
                                </select>
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Most Favorite Film</label>
                                <input type="text" class="form-control" name="most_favorite_film" value="{{ old('most_favorite_film', $user->most_favorite_film) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Most Favorite Song</label>
                                <input type="text" class="form-control" name="most_favorite_song" value="{{ old('most_favorite_song', $user->most_favorite_song) }}">
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Greatest Dream</label>
                                <input type="text" class="form-control" name="greatest_dream" value="{{ old('greatest_dream', $user->greatest_dream) }}">
                            </div>

                            <button type="submit" class="btn bg-gradient-dark">Save</button>
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
                        <h6 class="text-xs ps-3 text-muted">Ensure your account is using a long, random password to stay secure.</h6>
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        flatpickr("#birthday", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            yearRange: [1900, new Date().getFullYear()],
        });
    </script>

    <script>
        $('.select2').select2({
            placeholder: "Select a country",
            allowClear: true
        });
    </script>
@endpush
