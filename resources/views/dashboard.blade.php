@extends('layouts.master')
@push('css')
    <style>
        .search-container {
            margin-left: 20px;
        }
        .material-search-wrapper {
            display: flex;
            max-width: 400px;
            width: 100%;
            background: white;
            border: 1px solid #d2d6da;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .material-search-wrapper input[type="text"] {
            flex: 1;
            padding: 12px 16px;
            border: none;
            font-size: 14px;
            outline: none;
            color: #344767;
            background-color: transparent;
        }

        .material-search-wrapper button {
            padding: 0 16px;
            background-color: #4a90e2;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .material-search-wrapper button:hover {
            background-color: #357bd8;
        }

        .material-search-wrapper button i {
            font-size: 18px;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 px-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize mb-0">Contact's List</h6>

                        @if(isset($meta['total']))
                            <div class="text-sm text-white mb-0">
                                Total contacts: {{ $meta['total'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <div class="search-container">
                            <form method="GET" action="{{ route('dashboard') }}">
                                <div class="material-search-wrapper">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone">
                                    <button type="submit">
                                        <i class="material-symbols-rounded">search</i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contact</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Phone</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tags</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Activity</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
{{--                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <!-- Generate random color for the circle -->
                                                @php
                                                    $colors = ["#6c5ce7", "#00b894", "#0984e3", "#fd79a8", "#e17055", "#00cec9", "#d63031"];
                                                    $randomColor = $colors[array_rand($colors)];
                                                @endphp
                                                    <!-- Display the avatar with initials -->
                                                <div class="avatar avatar-sm me-3 border-radius-lg" style="background-color: {{ $randomColor }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($contact['firstName'], 0, 1)) }}{{ strtoupper(substr($contact['lastName'] ?? '', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $contact['firstName'] }} {{ $contact['lastName'] ?? '' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $contact['email'] ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $contact['phone'] ?? '-' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $contact['email'] ?? '-' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if(isset($contact['tags']) && is_array($contact['tags']))
                                            @foreach ($contact['tags'] as $tag)
                                                <span class="badge badge-sm me-1 {{$tag == 'order paid' ? 'bg-gradient-success' : 'bg-gradient-info'}}">{{ $tag }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            @if(isset($contact['lastActivity'])) {{ \Carbon\Carbon::parse($contact['lastActivity'] / 1000)->diffForHumans() }} @endif
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{\Carbon\Carbon::parse($contact['dateAdded'])->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('contacts.destroy', ['id' => $contact['id']]) }}" method="POST" class="delete-contact-form inline">
                                            @csrf
                                            @method('DELETE')

                                            <a href="{{ route('contacts.edit', $contact['id']) }}" class="text-secondary font-weight-bold text-xs me-2" data-toggle="tooltip" data-original-title="Edit user">
                                                <i class="material-symbols-rounded text-sm me-1">edit</i> Edit
                                            </a>

                                            <button type="submit" class="delete-button text-secondary font-weight-bold text-xs bg-transparent border-0" data-toggle="tooltip" data-original-title="Delete user">
                                                <i class="material-symbols-rounded text-sm me-1">delete</i> Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(isset($meta['nextPageUrl']))
                            <div class="mt-4 text-center">
                                @if(request()->has('startAfter') || request()->has('startAfterId'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-icon btn-3 btn-secondary" type="button">
                                        <span class="btn-inner--icon">
                                            <i class="material-symbols-rounded">first_page</i>
                                        </span>
                                        <span class="btn-inner--text">Go Back To Page 1</span>
                                    </a>
                                @endif

                                @if(isset($meta['startAfter']) && isset($meta['startAfterId']))
                                    <a href="{{ route('dashboard', [
                                        'startAfter' => $meta['startAfter'] ?? '',
                                        'startAfterId' => $meta['startAfterId'] ?? '',
                                        'search' => request('search')
                                    ]) }}" class="btn btn-info">
                                        Load More Contacts
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 text-center">
                                @if(isset($meta['startAfter']) && isset($meta['startAfterId']))
                                    <a href="{{ route('dashboard', [
                                        'startAfter' => $meta['startAfter'] ?? '',
                                        'startAfterId' => $meta['startAfterId'] ?? ''
                                    ]) }}" class="btn btn-icon btn-3 btn-secondary" type="button">
                                        <span class="btn-inner--icon">
                                            <i class="material-symbols-rounded">first_page</i>
                                        </span>
                                        <span class="btn-inner--text">Back</span>
                                    </a>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteForms = document.querySelectorAll('.delete-contact-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This contact will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@push('scripts')
    @if(session('status'))
        <script>
            setTimeout(function () {
                window.location.reload();
                console.log('reloading...1');

                setTimeout(function () {
                    window.location.reload();
                    console.log('reloading...2');
                }, 1000);
            }, 3000);
        </script>
    @endif
@endpush
