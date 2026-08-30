@extends('layouts.admin')

@section('title', 'Manage Comments')
@section('page_title', 'Comments Moderation')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Comments Moderation</h4>
            <p class="text-body-secondary small mb-0">Review, approve, and respond to reader discussions.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 bg-body">
        <div class="card-body p-2 d-flex flex-wrap gap-2">
            <a href="{{ route('admin.comments.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                All ({{ $stats['total'] }})
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                Approved ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'spam']) }}" class="btn btn-sm {{ request('status') === 'spam' ? 'btn-danger' : 'btn-outline-secondary' }}">
                Spam ({{ $stats['spam'] }})
            </a>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>In Response To</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $comment->author_avatar }}" alt="{{ $comment->author_name }}" class="rounded-circle" width="34" height="34">
                                    <div>
                                        <div class="fw-bold small">{{ $comment->author_name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $comment->guest_email ?? ($comment->user->email ?? '') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 small text-body">{{ $comment->content }}</p>
                                @if($comment->parent)
                                    <small class="text-muted"><i class="bi bi-arrow-return-right me-1"></i> In reply to {{ $comment->parent->author_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($comment->post)
                                    <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" class="small fw-semibold text-decoration-none">
                                        {{ Str::limit($comment->post->title, 35) }} <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">Post deleted</span>
                                @endif
                            </td>
                            <td>
                                @if($comment->status === 'approved')
                                    <span class="badge bg-success-subtle text-success">Approved</span>
                                @elseif($comment->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Spam</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $comment->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($comment->status !== 'approved')
                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif

                                    @if($comment->status !== 'spam')
                                        <form action="{{ route('admin.comments.spam', $comment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" title="Mark as Spam"><i class="bi bi-slash-circle"></i></button>
                                        </form>
                                    @endif

                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#replyCommentModal-{{ $comment->id }}" title="Reply as Admin">
                                        <i class="bi bi-reply"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCommentModal-{{ $comment->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Reply Modal -->
                                <div class="modal fade text-start" id="replyCommentModal-{{ $comment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.comments.reply', $comment->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Reply to {{ $comment->author_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="p-2 mb-3 bg-light rounded small text-muted">
                                                        <strong>Original Comment:</strong> "{{ $comment->content }}"
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label small fw-semibold">Your Official Reply</label>
                                                        <textarea name="content" class="form-control" rows="3" placeholder="Type your reply as Admin..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Post Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade text-start" id="deleteCommentModal-{{ $comment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger">Delete Comment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this comment?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No comments in this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $comments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
