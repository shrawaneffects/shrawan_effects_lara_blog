@extends('layouts.admin')

@section('title', 'Manage Users')
@section('page_title', 'Users & Roles')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">User Management</h4>
            <p class="text-body-secondary small mb-0">Manage roles, author permissions, and user accounts.</p>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="rounded-circle" width="38" height="38">
                                    <div>
                                        <div class="fw-bold">{{ $u->name }}</div>
                                        @if($u->id === auth()->id())
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $u->email }}</td>
                            <td>
                                @if($u->id === auth()->id())
                                    <span class="badge bg-danger">{{ ucfirst($u->role) }}</span>
                                @else
                                    <form action="{{ route('admin.users.update-role', $u->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                                            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="author" {{ $u->role === 'author' ? 'selected' : '' }}>Author</option>
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="small">
                                <span class="badge bg-light text-dark border">{{ $u->posts_count }} posts</span>
                                <span class="badge bg-light text-dark border ms-1">{{ $u->comments_count }} comments</span>
                            </td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Deactivated</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $u->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                @if($u->id !== auth()->id())
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.users.toggle-active', $u->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $u->is_active ? 'warning' : 'success' }}" title="{{ $u->is_active ? 'Deactivate User' : 'Activate User' }}">
                                                <i class="bi bi-{{ $u->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal-{{ $u->id }}" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade text-start" id="deleteUserModal-{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-danger">Delete User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to permanently delete user <strong>"{{ $u->name }}"</strong>? All associated articles and comments will also be removed.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
