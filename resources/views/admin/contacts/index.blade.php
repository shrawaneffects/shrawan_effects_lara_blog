@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page_title', 'Contact Messages')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Messages & Inquiries</h4>
            <p class="text-body-secondary small mb-0">Messages submitted through the public contact form.</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 bg-body">
        <div class="card-body p-2 d-flex gap-2">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                All Messages
            </a>
            <a href="{{ route('admin.contacts.index', ['status' => 'unread']) }}" class="btn btn-sm {{ request('status') === 'unread' ? 'btn-warning' : 'btn-outline-secondary' }}">
                Unread
            </a>
            <a href="{{ route('admin.contacts.index', ['status' => 'read']) }}" class="btn btn-sm {{ request('status') === 'read' ? 'btn-success' : 'btn-outline-secondary' }}">
                Read
            </a>
        </div>
    </div>

    <!-- Messages Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sender</th>
                        <th>Subject & Message</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr class="{{ !$contact->is_read ? 'table-warning-subtle fw-semibold' : '' }}">
                            <td>
                                <div class="fw-bold">{{ $contact->name }}</div>
                                <div class="text-muted small">
                                    <a href="mailto:{{ $contact->email }}" class="text-decoration-none">{{ $contact->email }}</a>
                                </div>
                            </td>
                            <td>
                                @if($contact->subject)
                                    <div class="fw-bold text-body small">{{ $contact->subject }}</div>
                                @endif
                                <p class="mb-0 small text-body">{{ $contact->message }}</p>
                            </td>
                            <td>
                                @if($contact->is_read)
                                    <span class="badge bg-secondary-subtle text-secondary">Read</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-envelope-fill me-1"></i> New</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $contact->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if(!$contact->is_read)
                                        <form action="{{ route('admin.contacts.read', $contact->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Mark as Read">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this message?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No contact messages in this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $contacts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
