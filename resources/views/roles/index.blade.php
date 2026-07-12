@extends('layouts.app')

@section('title', 'Hak Akses & Otorisasi Sistem')
@section('page-title', 'Hak Akses & Otorisasi (RBAC)')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">RBAC</li>
</ol>
@endsection

@section('content')
<!-- Roles Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Matriks Peran (Role) & Otorisasi (Permission)</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th style="width: 25%;">Hak Akses (Role)</th>
                    <th style="width: 75%;">Granular Permissions (Otorisasi Terkait)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>
                            @php
                                $badgeColor = 'bg-secondary';
                                if ($role->name === 'Staff') $badgeColor = 'bg-primary';
                                elseif ($role->name === 'Supervisor') $badgeColor = 'bg-info text-dark';
                                elseif ($role->name === 'Manager') $badgeColor = 'bg-warning text-dark';
                                elseif ($role->name === 'Director') $badgeColor = 'bg-purple text-white';
                                elseif ($role->name === 'Finance') $badgeColor = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeColor }} py-2 px-3 fs-6" style="{{ $role->name === 'Director' ? 'background-color: #7c3aed !important;' : '' }}">{{ $role->name }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1.5" style="gap: 6px;">
                                @foreach($permissions as $permission)
                                    @php
                                        $hasPerm = $role->hasPermissionTo($permission->name);
                                    @endphp
                                    <span class="badge {{ $hasPerm ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' }} py-1.5 px-2.5 small" style="font-size: 0.75rem;">
                                        <i class="bi {{ $hasPerm ? 'bi-check-circle-fill me-1' : 'bi-x-circle me-1' }}"></i>
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
