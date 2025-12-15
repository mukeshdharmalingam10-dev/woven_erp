@extends('layouts.dashboard')

@section('title', 'Branch Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Branch Details</h2>
        <div style="display: flex; gap: 10px;">
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('branches.edit', $branch->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">Edit</a>
            @endif
            <a href="{{ route('branches.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Name</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $branch->name }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Address</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0; line-height: 1.6;">
                @if($branch->address_line_1)
                    {{ $branch->address_line_1 }}<br>
                    @if($branch->address_line_2)
                        {{ $branch->address_line_2 }}<br>
                    @endif
                    {{ $branch->city }}, {{ $branch->state }} - {{ $branch->pincode }}
                @else
                    N/A
                @endif
            </p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Contact Information</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $branch->phone ?? 'N/A' }}</p>
        </div>
    </div>

    @if($branch->description)
        <div style="margin-bottom: 30px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
            <p style="color: #333; font-size: 16px; margin: 0;">{{ $branch->description }}</p>
        </div>
    @endif

    <div style="margin-top: 30px;">
        <h3 style="color: #333; margin-bottom: 15px;">Users ({{ $branch->users->count() }})</h3>
        @if($branch->users->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Name</th>
                            <th style="padding: 12px; text-align: left;">Email</th>
                            <th style="padding: 12px; text-align: left;">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branch->users as $user)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;">{{ $user->name }}</td>
                                <td style="padding: 12px;">{{ $user->email }}</td>
                                <td style="padding: 12px;">{{ $user->role ? $user->role->name : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: #999;">No users found.</p>
        @endif
    </div>
</div>
@endsection

