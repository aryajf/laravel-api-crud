<?php

namespace App\Http\Controllers;

use App\Helpers\Validation;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['user:id,username','comments'])->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validation::inputCheck($request->all(), 'Gagal Menambahkan Post', [
            'title' => 'required',
            'news_content' => 'required',
            'file_image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ],[
            'title.required' => 'Judul harus diisi',
            'news_content.required' => 'Konten harus diisi',
            'file_image.required' => 'Image harus diisi',
            'file_image.mimes' => 'Image harus foto berformat JPEG, PNG, JPG, GIF, SVG',
            'file_image.max' => 'Maksimal ukuran Image harus 4 MB',
        ]);

        if($request->file('file_image')){
            $filename = Str::random(15).'.'.$request->file('file_image')->getClientOriginalExtension();
            $request['image'] = $filename;
            Storage::putFileAs('image', $request->file('file_image'), $filename);
        }

        $request['user_id'] = Auth::user()->id;

        $post = Post::create($request->all());
        return new PostResource($post->loadMissing(['user:id,username','comments']), $post->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with(['user:id,username','comments'])->find($id);
        if(!$post){
            return response()->json(['status' => false, 'message' => 'Post tidak ditemukan'], 404);
        }
        return new PostResource($post, $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validation::inputCheck($request->all(), 'Gagal Memperbarui Post', [
            'title' => 'required',
            'news_content' => 'required',
            'file_image' => 'mimes:jpeg,png,jpg,gif,svg|max:4096',
        ],[
            'title.required' => 'Judul harus diisi',
            'news_content.required' => 'Konten harus diisi',
            'file_image.mimes' => 'Image harus foto berformat JPEG, PNG, JPG, GIF, SVG',
            'file_image.max' => 'Maksimal ukuran Image harus 4 MB',
        ]);

        $post = Post::find($id);
        if(!$post){
            return response()->json(['status' => false, 'message' => 'Post tidak ditemukan'], 404);
        }

        if($request->file('file_image')){
            $filename = Str::random(15).'.'.$request->file('file_image')->getClientOriginalExtension();
            Storage::putFileAs('image', $request->file('file_image'), $filename);
            if (Storage::exists('image/' . $post->image)) {
                Storage::delete('image/' . $post->image);
            }
            $request['image'] = $filename;
        }

        try {
            $this->authorize('AccessPost', $post);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Post tidak ditemukan'], 404);
        }

        $post->update($request->all());
        return new PostResource($post->loadMissing(['user:id,username','comments']), $post->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if(!$post){
            return response()->json(['status' => false, 'message' => 'Post tidak ditemukan'], 404);
        }

        try {
            $this->authorize('AccessPost', $post);
        } catch (AuthorizationException $e) {
            return response()->json(['status' => false, 'message' => 'Post tidak ditemukan'], 404);
        }

        if (Storage::exists('image/' . $post->image)) {
            Storage::delete('image/' . $post->image);
        }

        $post->delete();
        return response()->json(['status' => true, 'message' => 'Post berhasil dihapus']);
    }
}
