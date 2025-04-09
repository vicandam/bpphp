@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Contact's List</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contact</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Phone</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tags</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Activity</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
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
                                                <span class="badge badge-sm bg-gradient-info me-1">{{ $tag }}</span>
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
                                        <form action="{{ route('contacts.destroy', ['id' => $contact['id']]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this contact?');">
                                            @csrf
                                            @method('DELETE')
                                            <a href="{{ route('contacts.edit', $contact['id']) }}" class="btn btn-outline-secondary text-xs" data-toggle="tooltip" title="Edit contact">
                                                Edit
                                            </a>
                                            <button type="submit" class="btn btn-primary btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if(session('status'))
        <script>
            setTimeout(function () {
                window.location.reload();
                console.log('reloading...');
            }, 3000);
        </script>
    @endif
@endpush
