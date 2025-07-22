<!-- resources/views/users/edit.blade.php -->
@extends('layouts.master')

@section('title', 'Edit My Profile')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit My Profile</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('users.update', $user) }}" class="p-4">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">Mobile No.</label>
                                    <input type="text" class="form-control" name="mobile_no" value="{{ old('mobile_no', $user->mobile_no) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" class="form-control" name="birthday" value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">City or Province</label>
                                    <input type="text" class="form-control" name="city_or_province" value="{{ old('city_or_province', $user->city_or_province) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline is-filled my-3">
                                    <label class="form-label">Country (if outside PH)</label>
                                    <input type="text" class="form-control" name="country" value="{{ old('country', $user->country) }}">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-outline is-filled my-3">
                            <label class="form-label">Most Favorite Film</label>
                            <input type="text" class="form-control" name="most_favorite_film" value="{{ old('most_favorite_film', $user->most_favorite_film) }}">
                        </div>

                        <div class="input-group input-group-outline is-filled my-3">
                            <label class="form-label">Most Favorite Song</label>
                            <input type="text" class="form-control" name="most_favorite_song" value="{{ old('most_favorite_song', $user->most_favorite_song) }}">
                        </div>

                        <div class="input-group input-group-outline is-filled my-3">
                            <label class="form-label">Greatest Dream</label>
                            <textarea class="form-control" name="greatest_dream" rows="5">{{ old('greatest_dream', $user->greatest_dream) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
