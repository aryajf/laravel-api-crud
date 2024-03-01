<?php

namespace App\Http\Controllers;

use App\Helpers\Validation;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validation::inputCheck($request->all(), 'Gagal Menambahkan Komentar', [
            'post_id' => 'required',
            'comments_content' => 'required'
        ],[
            'post_id.required' => 'Post harus diisi',
            'comments_content.required' => 'Komentar harus diisi',
        ]);

        $post = Post::find($request->post_id);
        if(!$post){
            return response()->json(['status' => false, 'message' => 'Komentar tidak ditemukan'], 404);
        }

        $request['user_id'] = Auth::user()->id;

        $comment = Comment::create($request->all());
        return new CommentResource($comment->loadMissing(['user:id,username','post:id,title']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validation::inputCheck($request->all(), 'Gagal Memperbarui Komentar', [
            'comments_content' => 'required'
        ],[
            'comments_content.required' => 'Komentar harus diisi',
        ]);

        $comment = Comment::find($id);
        if(!$comment){
            return response()->json(['status' => false, 'message' => 'Komentar tidak ditemukan'], 404);
        }

        try {
            $this->authorize('AccessComment', $comment);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Komentar tidak ditemukan'], 404);
        }

        $comment->update($request->only('comments_content'));
        return new CommentResource($comment->loadMissing(['user:id,username','post:id,title']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        if(!$comment){
            return response()->json(['status' => false, 'message' => 'Komentar tidak ditemukan'], 404);
        }

        try {
            $this->authorize('AccessComment', $comment);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Komentar tidak ditemukan'], 404);
        }

        $comment->delete();
        return response()->json(['status' => true, 'message' => 'Komentar berhasil dihapus']);
    }
}
