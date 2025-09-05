@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Profile Information</h6>
                        <h6 class="text-xs ps-3 text-white">Update your account's profile information and email address.</h6>
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

                            <button type="submit" class="btn bg-gradient-primary">Save</button>
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
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Update Password</h6>
                        <h6 class="text-xs ps-3 text-white">Ensure your account is using a long, random password to stay secure.</h6>
                    </div>

                    <div class="p-4">
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="input-group input-group-static mb-4">
                                <label>New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="new_password">
                                    <span class="input-group-text" onclick="togglePassword('new_password', this)" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
                            </div>

                            <div class="input-group input-group-static mb-4">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation" id="confirm_password">
                                    <span class="input-group-text" onclick="togglePassword('confirm_password', this)" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
                            </div>


                            <button type="submit" class="btn bg-gradient-primary">Save</button>
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

    <script>
        function togglePassword(fieldId, el) {
            const input = document.getElementById(fieldId);
            const icon = el.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endpush
