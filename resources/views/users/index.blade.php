@extends('layouts.app')

@section('title', 'Manajemen Pengguna Sistem')
@section('page-title', 'Pengguna Sistem')

@section('breadcrumbs')
<ol class="breadcrumb custom-breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pengguna Sistem</li>
</ol>
@endsection

@section('content')
<!-- Users Table Card -->
<div class="card custom-table-card border-0">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Daftar Pengguna</h5>
        <button class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus"></i> Tambah Pengguna Baru
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Hak Akses (Role)</th>
                    <th>Tanggal Terdaftar</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $userItem)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $userItem->name }}</td>
                        <td>{{ $userItem->email }}</td>
                        <td>
                            @php
                                $roleName = $userItem->roles->first()->name ?? 'N/A';
                                $badgeColor = 'bg-secondary';
                                if ($roleName === 'Staff') $badgeColor = 'bg-primary';
                                elseif ($roleName === 'Supervisor') $badgeColor = 'bg-info text-dark';
                                elseif ($roleName === 'Manager') $badgeColor = 'bg-warning text-dark';
                                elseif ($roleName === 'Director') $badgeColor = 'bg-purple text-white';
                                elseif ($roleName === 'Finance') $badgeColor = 'bg-success';
                            @endphp
                            <span class="badge {{ $badgeColor }} py-1 px-2.5" style="{{ $roleName === 'Director' ? 'background-color: #7c3aed !important;' : '' }}">{{ $roleName }}</span>
                        </td>
                        <td>{{ $userItem->created_at->format('d F Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Edit Button (trigger Modal) -->
                                <button class="btn btn-sm btn-warning text-white px-2 py-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal{{ $userItem->id }}" 
                                        title="Ubah Data Pengguna">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('users.destroy', $userItem) }}" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger px-2 py-1 btn-delete-user" title="Hapus Akun Pengguna">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal{{ $userItem->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $userItem->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('users.update', $userItem) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $userItem->id }}">Ubah Data Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $userItem->id }}" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control bg-light" value="{{ old('name', $userItem->name) }}" required>
                                        </div>
                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $userItem->email) }}" required>
                                        </div>
                                        <!-- Password (Optional) -->
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Password Baru <span class="text-muted">(Kosongkan jika tidak ingin diubah)</span></label>
                                            <input type="password" name="password" class="form-control bg-light" placeholder="Minimal 8 karakter">
                                        </div>
                                        <!-- Role -->
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Hak Akses (Role) <span class="text-danger">*</span></label>
                                            <select name="role" class="form-select bg-light" required>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ old('role', $roleName) === $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary-premium">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2"></i>
                            <p class="mt-2 mb-0">Belum ada akun pengguna terdaftar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createUserModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-light" placeholder="Contoh: Andi Wijaya" value="{{ old('name') }}" required>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-light" placeholder="Contoh: andi@company.com" value="{{ old('email') }}" required>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control bg-light" placeholder="Minimal 8 karakter" required>
                    </div>
                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select bg-light" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-premium">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation for delete
        const deleteButtons = document.querySelectorAll('.btn-delete-user');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus Pengguna?',
                    text: 'Akun ini akan dihapus permanen. Tindakan ini akan gagal jika pengguna telah mengajukan transaksi pengeluaran.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
