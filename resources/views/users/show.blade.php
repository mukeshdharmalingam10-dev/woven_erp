@extends('layouts.dashboard')

@section('title', 'User Details - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">User Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('users.edit', $user->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Edit
            </a>
            <a href="{{ route('users.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Name</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $user->name }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Email</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $user->email }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Mobile</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $user->mobile ?? 'N/A' }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Role</label>
            <p style="margin: 0 0 20px 0;">
                @if($user->role)
                    <span style="background: #667eea; color: white; padding: 6px 15px; border-radius: 12px; font-size: 14px;">
                        {{ $user->role->name }}
                    </span>
                @else
                    <span style="color: #999;">No Role Assigned</span>
                @endif
            </p>
        </div>

        {{-- Branches - Hidden for all users including superadmin --}}
        <div style="display: none; grid-column: 1 / -1;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Branches</label>
            <p style="margin: 0 0 20px 0;">
                @if($user->branches && $user->branches->count() > 0)
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($user->branches as $branch)
                            <span style="background: #f59e0b; color: white; padding: 6px 15px; border-radius: 12px; font-size: 14px;">
                                {{ $branch->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span style="color: #999;">No Branches Assigned</span>
                @endif
            </p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $user->created_at->format('M d, Y') }}</p>
        </div>
    </div>
</div>
@endsection

