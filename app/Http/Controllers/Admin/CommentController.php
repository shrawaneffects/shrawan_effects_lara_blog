<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['post', 'user', 'parent']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('content', 'LIKE', "%{$term}%")
                  ->orWhere('guest_name', 'LIKE', "%{$term}%")
                  ->orWhere('guest_email', 'LIKE', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'LIKE', "%{$term}%");
                  });
            });
        }

        $comments = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Comment::count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
        ];

        return view('admin.comments.index', compact('comments', 'stats'));
    }

    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Comment approved successfully!');
    }

    public function spam($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => 'spam']);

        return redirect()->back()->with('success', 'Comment marked as spam!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }

    public function reply(Request $request, $id)
    {
        $parent = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:2000',
        ]);

        Comment::create([
            'post_id' => $parent->post_id,
            'user_id' => auth()->id(),
            'parent_id' => $parent->id,
            'content' => $validated['content'],
            'status' => 'approved',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Admin reply posted successfully!');
    }
}
