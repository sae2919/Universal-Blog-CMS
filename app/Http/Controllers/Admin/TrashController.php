<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;
use App\Models\Comment;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $posts = Post::onlyTrashed()->with('category', 'author')->latest()->get();
        $pages = Page::onlyTrashed()->latest()->get();
        $comments = Comment::onlyTrashed()->with('post')->latest()->get();

        return view('admin.trash.index', compact('posts', 'pages', 'comments'));
    }

    public function restore(string $type, int $id)
    {
        $model = $this->getModel($type, $id);
        $model->restore();

        return redirect()->route('admin.trash.index')
            ->with('success', ucfirst($type) . ' successfully restored.');
    }

    public function forceDelete(string $type, int $id)
    {
        $model = $this->getModel($type, $id);
        $model->forceDelete();

        return redirect()->route('admin.trash.index')
            ->with('success', ucfirst($type) . ' permanently deleted.');
    }

    protected function getModel(string $type, int $id)
    {
        switch ($type) {
            case 'post':
                return Post::onlyTrashed()->findOrFail($id);
            case 'page':
                return Page::onlyTrashed()->findOrFail($id);
            case 'comment':
                return Comment::onlyTrashed()->findOrFail($id);
            default:
                abort(404, 'Invalid trash item type.');
        }
    }
}
