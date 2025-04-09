@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('status'))
                <div class="alert alert-success text-white" role="alert" style="margin-bottom: 50px!important;">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Account Settings</h6>
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

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
