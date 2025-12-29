@extends('layouts.dashboard')

@section('title', 'Branches - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Branches</h2>
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('branches.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Add New Branch
            </a>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($branches->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">S.No</th>
                        <th style="padding: 12px; text-align: left;">Name</th>
                        <th style="padding: 12px; text-align: left;">Location</th>
                        <th style="padding: 12px; text-align: left;">Users</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ ($branches->currentPage() - 1) * $branches->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; font-weight: 500;">{{ $branch->name }}</td>
                            <td style="padding: 12px;">
                                @if($branch->address_line_1)
                                    {{ $branch->address_line_1 }}, {{ $branch->city }}, {{ $branch->state }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td style="padding: 12px;">{{ $branch->users->count() }} user(s)</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('branches.show', $branch->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">View</a>
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('branches.edit', $branch->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">Edit</a>
                                        <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['paginator' => $branches, 'routeUrl' => route('branches.index')])
    @else
        <div style="text-align: center; padding: 40px;">
            <p>No branches found.</p>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('branches.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; display: inline-block;">Create First Branch</a>
            @endif
        </div>
    @endif
</div>
@endsection
