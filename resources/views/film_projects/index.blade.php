<!-- resources/views/film_projects/index.blade.php -->
@extends('layouts.master')

@section('title', 'Dream Film Projects')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Our Dream Film Projects</h6>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('film_projects.create') }}" class="btn btn-white btn-sm mb-0 me-3">Add New Dream Film Project</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($filmProjects->isEmpty())
                            <p class="text-center text-muted py-4">No film projects found at the moment. Check back later!</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Target Fund</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Net Sales</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($filmProjects as $project)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">movie</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $project->title }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ Str::limit($project->description, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                        <span class="badge badge-sm bg-gradient-{{
                                            $project->status == 'Released' ? 'success' :
                                            ($project->status == 'Production' ? 'info' :
                                            ($project->status == 'Post-production' ? 'warning' : 'secondary'))
                                        }}">{{ $project->status }}</span>
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($project->target_fund_amount, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">₱{{ number_format($project->total_net_theatrical_ticket_sales, 2) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('film_projects.show', $project) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Dream Film Project">
                                                View
                                            </a>
                                            @if(Auth::user() && Auth::user()->is_admin)
                                                <a href="{{ route('film_projects.edit', $project) }}" class="text-secondary font-weight-bold text-xs ms-3" data-toggle="tooltip" data-original-title="Edit Dream Film Project">
                                                    Edit
                                                </a>
                                                <form action="{{ route('film_projects.destroy', $project) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this dream film project?')">Delete</button>
                                                </form>
                                                <form action="{{ route('film_projects.calculate_shares', $project) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link text-info text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to calculate and distribute Angel Investor shares for this project? This action cannot be undone.')">Calculate Shares</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
