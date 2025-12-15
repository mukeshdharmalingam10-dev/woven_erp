@extends('layouts.dashboard')

@section('title', 'Dashboard - Woven_ERP')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; padding: 40px;">
    <!-- Company Logo -->
    <div style="margin-bottom: 30px;">
        <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
            <i class="fas fa-layer-group" style="font-size: 80px; color: white;"></i>
        </div>
    </div>
    
    <!-- Company Name -->
    <div style="text-align: center;">
        <h1 style="color: #333; font-size: 42px; font-weight: 700; margin-bottom: 10px; letter-spacing: 1px;">Woven_ERP</h1>
        <p style="color: #666; font-size: 18px; margin-top: 0;">Enterprise Resource Planning</p>
    </div>
</div>
@endsection

