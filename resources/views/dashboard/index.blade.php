@extends('layouts.dashboard')

@section('title', 'Dashboard - Woven_ERP')

@section('content')
<div style="padding: 0;">
    <!-- Welcome Section -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; border-radius: 15px; margin-bottom: 30px; color: white; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 8px; color: white;">Welcome back, {{ $user->name }}!</h1>
                <p style="font-size: 16px; opacity: 0.9; margin: 0;">
                    @if(isset($companyName) && $companyName)
                        {{ $companyName }}
                        @if($user->branch)
                            - {{ $user->branch->name }}
                        @endif
                    @elseif($user->organization)
                        {{ $user->organization->name }}
                        @if($user->branch)
                            - {{ $user->branch->name }}
                        @endif
                    @else
                        Woven ERP System
                    @endif
                </p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="text-align: right;">
                    <div style="font-size: 14px; opacity: 0.8;">Today</div>
                    <div style="font-size: 24px; font-weight: 600;">{{ now()->format('d M Y') }}</div>
                </div>
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 15px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <i class="fas fa-calendar-alt" style="font-size: 24px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Customers Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #667eea;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('customers.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['customers']) }}</div>
                    <div style="font-size: 14px; color: #666;">Customers</div>
                </div>
            </div>
            <div style="font-size: 13px; color: #999; margin-top: 10px;">
                <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>View all customers
            </div>
        </div>

        <!-- Products Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #28a745;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(40, 167, 69, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('products.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-box" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['products']) }}</div>
                    <div style="font-size: 14px; color: #666;">Products</div>
                </div>
            </div>
            <div style="font-size: 13px; color: #999; margin-top: 10px;">
                <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>View all products
            </div>
        </div>

        <!-- Employees Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #ffc107;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(255, 193, 7, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('employees.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-tie" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['employees']) }}</div>
                    <div style="font-size: 14px; color: #666;">Employees</div>
                </div>
            </div>
            <div style="font-size: 13px; color: #999; margin-top: 10px;">
                <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>View all employees
            </div>
        </div>

        <!-- Work Orders Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #17a2b8;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(23, 162, 184, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('work-orders.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clipboard-list" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['work_orders']) }}</div>
                    <div style="font-size: 14px; color: #666;">Work Orders</div>
                </div>
            </div>
            <div style="font-size: 13px; color: #999; margin-top: 10px;">
                <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>View all work orders
            </div>
        </div>
    </div>

    <!-- Second Row Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Productions Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #6f42c1;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(111, 66, 193, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('productions.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-industry" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['productions']) }}</div>
                    <div style="font-size: 14px; color: #666;">Productions</div>
                </div>
            </div>
        </div>

        <!-- Sales Invoices Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #20c997;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(32, 201, 151, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('sales-invoices.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-invoice-dollar" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['sales_invoices']) }}</div>
                    <div style="font-size: 14px; color: #666;">Sales Invoices</div>
                </div>
            </div>
        </div>

        <!-- Raw Materials Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #fd7e14;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(253, 126, 20, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('raw-materials.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cubes" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['raw_materials']) }}</div>
                    <div style="font-size: 14px; color: #666;">Raw Materials</div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; border-left: 4px solid #6610f2;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(102, 16, 242, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)';" onclick="window.location.href='{{ route('purchase-orders.index') }}'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-shopping-cart" style="font-size: 24px; color: white;"></i>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 32px; font-weight: 700; color: #333;">{{ number_format($stats['purchase_orders']) }}</div>
                    <div style="font-size: 14px; color: #666;">Purchase Orders</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Recent Activities -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
        <!-- Quick Actions -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bolt" style="color: #ffc107;"></i>
                Quick Actions
            </h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                @if(Route::has('work-orders.create'))
                <a href="{{ route('work-orders.create') }}" style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-plus-circle" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">New Work Order</span>
                </a>
                @endif
                @if(Route::has('productions.create'))
                <a href="{{ route('productions.create') }}" style="padding: 15px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-industry" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">Production Entry</span>
                </a>
                @endif
                @if(Route::has('sales-invoices.create'))
                <a href="{{ route('sales-invoices.create') }}" style="padding: 15px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-file-invoice" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">Sales Invoice</span>
                </a>
                @endif
                @if(Route::has('attendances.create'))
                <a href="{{ route('attendances.create') }}" style="padding: 15px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-calendar-check" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">Mark Attendance</span>
                </a>
                @endif
                @if(Route::has('petty-cash.create'))
                <a href="{{ route('petty-cash.create') }}" style="padding: 15px; background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-money-bill-wave" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">Daily Expense</span>
                </a>
                @endif
                @if(Route::has('leaves.create'))
                <a href="{{ route('leaves.create') }}" style="padding: 15px; background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); color: white; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">
                    <i class="fas fa-calendar-times" style="font-size: 24px;"></i>
                    <span style="font-weight: 500;">Leave Request</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Recent Activities -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; font-weight: 600; color: #333; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-history" style="color: #667eea;"></i>
                Recent Activities
            </h3>
            <div style="max-height: 400px; overflow-y: auto;">
                @if(count($recentActivities) > 0)
                    @foreach($recentActivities as $activity)
                        <div style="display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid #f0f0f0; transition: all 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='white';">
                            <div style="width: 40px; height: 40px; background: {{ $activity['color'] }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas {{ $activity['icon'] }}" style="color: white; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #333; margin-bottom: 4px;">{{ $activity['title'] }}</div>
                                <div style="font-size: 13px; color: #666; margin-bottom: 4px;">{{ $activity['description'] }}</div>
                                <div style="font-size: 12px; color: #999;">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>No recent activities</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .stat-card {
            min-width: 100%;
        }
    }
    
    /* Smooth scrollbar for activities */
    div[style*="overflow-y: auto"]::-webkit-scrollbar {
        width: 6px;
    }
    
    div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endsection
