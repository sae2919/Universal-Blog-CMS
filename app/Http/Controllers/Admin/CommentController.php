<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('post')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        $this->clearPostCache($comment->post);
        return back()->with('success', 'Comment approved!');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);
        $this->clearPostCache($comment->post);
        return back()->with('success', 'Comment rejected!');
    }

    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        $comment->delete();
        if ($post) {
            $this->clearPostCache($post);
        }
        return back()->with('success', 'Comment deleted!');
    }

    private function clearPostCache($post)
    {
        if ($post) {
            \Illuminate\Support\Facades\Cache::forget("post.{$post->slug}.en");
            \Illuminate\Support\Facades\Cache::forget("post.{$post->slug}.fr");
            \Illuminate\Support\Facades\Cache::forget("post.{$post->slug}.de");
            \Illuminate\Support\Facades\Cache::forget("post.{$post->slug}.hi");
            \Illuminate\Support\Facades\Cache::forget("post.{$post->slug}.te");
        }
    }
}
