<!-- resources/views/film_projects/create.blade.php -->
@extends('layouts.master')

@section('title', 'Create New Dream Film Project')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Create New Dream Film Project</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="POST" action="{{ route('film_projects.store') }}" class="p-4">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Dream Film Project Title</label>
                            <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Link</label>
                            <input type="text" class="form-control" name="link" value="{{ old('link') }}" required>
                        </div>


                        <div class="form-group my-3">
                            <label for="status" class="form-label">Status</label>
                            <div class="input-group input-group-outline">
                                <select class="form-control" name="status" id="status" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="Pre-production" {{ old('status') == 'Pre-production' ? 'selected' : '' }}>Pre-production</option>
                                    <option value="Production" {{ old('status') == 'Production' ? 'selected' : '' }}>Production</option>
                                    <option value="Post-production" {{ old('status') == 'Post-production' ? 'selected' : '' }}>Post-production</option>
                                    <option value="Released" {{ old('status') == 'Released' ? 'selected' : '' }}>Released</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Target Fund Amount (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="target_fund_amount" value="{{ old('target_fund_amount') }}">
                        </div>

                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Total Net Theatrical Ticket Sales (PHP)</label>
                            <input type="number" step="0.01" class="form-control" name="total_net_theatrical_ticket_sales" value="{{ old('total_net_theatrical_ticket_sales') }}">
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('film_projects.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Create Dream Film Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
