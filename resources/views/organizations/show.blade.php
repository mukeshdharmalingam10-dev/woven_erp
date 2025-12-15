@extends('layouts.dashboard')

@section('title', 'Organization Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Organization Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('organizations.edit', $organization->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">Edit</a>
            <a href="{{ route('organizations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Name</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $organization->name }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Code</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $organization->code }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Email</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $organization->email ?? 'N/A' }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Phone</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $organization->phone ?? 'N/A' }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Admin</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $organization->admin ? $organization->admin->name : 'N/A' }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">
                <span style="background: {{ $organization->is_active ? '#48bb78' : '#dc3545' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                    {{ $organization->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
    </div>

    @if($organization->description)
        <div style="margin-bottom: 30px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
            <p style="color: #333; font-size: 16px; margin: 0;">{{ $organization->description }}</p>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <h3 style="color: #333; margin-bottom: 15px;">Branches ({{ $organization->branches->count() }})</h3>
        @if($organization->branches->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Name</th>
                            <th style="padding: 12px; text-align: left;">Code</th>
                            <th style="padding: 12px; text-align: left;">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organization->branches as $branch)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;">{{ $branch->name }}</td>
                                <td style="padding: 12px;">{{ $branch->code }}</td>
                                <td style="padding: 12px;">{{ $branch->admin ? $branch->admin->name : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: #999;">No branches found.</p>
        @endif
    </div>
</div>
@endsection

