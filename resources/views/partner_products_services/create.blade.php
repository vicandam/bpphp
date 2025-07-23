@extends('layouts.master')

@section('title', 'Add Product/Service for ' . $businessPartner->name)

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Add Product/Service for {{ $businessPartner->name }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('business_partners.products_services.store', $businessPartner) }}" class="p-4">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Product/Service Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Price (PHP)</label>
                                    <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Points for Redemption</label>
                                    <input type="number" step="0.01" class="form-control" name="points_for_redemption" value="{{ old('points_for_redemption') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center mb-3 mt-4">
                            <input class="form-check-input" type="checkbox" id="is_voucher" name="is_voucher" value="1" {{ old('is_voucher') ? 'checked' : '' }}>
                            <label class="form-check-label mb-0 ms-3" for="is_voucher">Is this a voucher?</label>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('business_partners.show', $businessPartner) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Add Product/Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
