@extends('layouts.dashboard')

@section('title', 'Organizations - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Organizations</h2>
        <a href="{{ route('organizations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add New Organization
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('viewing_organization_id'))
        @php
            $viewingOrg = \App\Models\Organization::find(session('viewing_organization_id'));
        @endphp
        @if($viewingOrg)
            <div style="background: #e7f3ff; color: #004085; padding: 12px; border-radius: 5px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <span>
                    <strong>Viewing Organization:</strong> {{ $viewingOrg->name }}
                </span>
                <a href="{{ route('organization.switch.clear') }}" style="padding: 6px 12px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                    View All Organizations
                </a>
            </div>
        @endif
    @endif

    @if($organizations->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">S.No</th>
                        <th style="padding: 12px; text-align: left;">Name</th>
                        <th style="padding: 12px; text-align: left;">Code</th>
                        <th style="padding: 12px; text-align: left;">Admin</th>
                        <th style="padding: 12px; text-align: left;">Branches</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organizations as $org)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ ($organizations->currentPage() - 1) * $organizations->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; font-weight: 500;">{{ $org->name }}</td>
                            <td style="padding: 12px;">{{ $org->code }}</td>
                            <td style="padding: 12px;">{{ $org->admin ? $org->admin->name : 'N/A' }}</td>
                            <td style="padding: 12px;">{{ $org->branches->count() }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('organizations.show', $org->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">View</a>
                                    <a href="{{ route('organizations.edit', $org->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">Edit</a>
                                    <a href="{{ route('organization.switch', $org->id) }}" style="padding: 6px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Switch to this organization for reporting">Switch</a>
                                    <form action="{{ route('organizations.destroy', $org->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination', ['paginator' => $organizations, 'routeUrl' => route('organizations.index')])
    @else
        <div style="text-align: center; padding: 40px;">
            <p>No organizations found.</p>
            <a href="{{ route('organizations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; display: inline-block;">Create First Organization</a>
        </div>
    @endif
</div>
@endsection

