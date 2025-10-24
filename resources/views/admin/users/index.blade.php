@extends('layouts.master')

@section('title', 'Admin - User Management')
@push('css')
{{--    <style>--}}
{{--        .table.align-items-center td {--}}
{{--            display: table-cell !important;--}}
{{--        }--}}
{{--    </style>--}}
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">All Users</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-4">
                        <form action="{{ route('index.users') }}" method="GET" class="mb-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group input-group-outline w-100">
                                        <label class="form-label">Search by Name, Email, or Referral Code</label>
                                        <input type="text" name="search" class="form-control" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-outline w-100">
                                        <select name="filter" class="form-control">
                                            <option value="">Filter by Membership</option>
                                            @foreach($membershipTypes as $type)
                                                <option value="{{ $type->id }}" {{ request('filter') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 d-grid">
                                    <button type="submit" class="btn btn-primary mb-0">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive p-0">
                        @if($users->isEmpty())
                            <p class="text-center text-muted py-4">No users found matching your criteria.</p>
                        @else
                            <form id="bulkDeleteForm" action="{{ route('users.bulkDelete') }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <table class="table align-items-center mb-0">
                                    <thead>
                                    <tr>
                                        <th class="text-center align-middle"><input type="checkbox" id="selectAll"></th>
                                        <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                        <th class="text-center align-middle text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Membership</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referral Code</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                                        <th class="text-secondary opacity-7 text-uppercase text-secondary text-xxs font-weight-bolder">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-checkbox">
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <i class="material-icons text-lg me-3">person</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->membershipType->name ?? 'N/A' }}</p>
                                            </td>
                                            <td class="text-center align-middle" class="align-middle text-center text-sm">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $user->referral_code ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-center align-middle" class="align-middle text-center text-sm">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('show.users', $user) }}"
                                                   class="text-secondary font-weight-bold text-xs me-3">
                                                    View
                                                </a>

                                                {{-- Single delete --}}
                                                <form action="{{ route('users.destroy', $user->id) }}"
                                                      method="POST"
                                                      style="display:inline;"
                                                      onsubmit="return confirm('Delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-danger font-weight-bold text-xs border-0 bg-transparent p-0">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {{-- 🗑️ Bulk delete button --}}
                                <div class="p-4 mt-3">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirmBulkDelete()">
                                        Delete Selected
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="p-4">
{{--                        {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}--}}
                        {{ $users->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- JS for checkboxes and confirm --}}
    <script>
        document.getElementById('selectAll').addEventListener('click', function(e) {
            let checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });

        function confirmBulkDelete() {
            const checked = document.querySelectorAll('.user-checkbox:checked').length;
            if (checked === 0) {
                alert('Please select at least one user to delete.');
                return false;
            }
            return confirm('Are you sure you want to delete the selected users?');
        }
    </script>
@endpush
