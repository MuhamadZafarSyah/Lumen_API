<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    private function deleteImage(Post $post)
    {
        if ($post->image) {
            $filename = basename($post->image);
            $path = base_path('public/uploads/' . $filename);

            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'message' => 'Success get All Posts',
            'status' => 'oke',
            'posts' => $posts,
        ], 200);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 'error',
            ], 404);
        }

        return response()->json([
            'message' => 'Success get post',
            'status' => 'oke',
            'post' => $post,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required|min:4|max:255',
            'body' => 'required|min:4',
            'image' => 'required|image|file',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        if ($request->hasFile('image')) {
            $name = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move('uploads', $name);
        }

        $validatedData = $validator->validated();
        $validatedData['image'] = url('uploads' . '/' . $name);

        Post::create($validatedData);

        return response()->json([
            'message' => 'Success create post',
            'status' => 'oke',
        ], 200);
    }



    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 'error',
            ], 404);
        }

        if ($post->image) {
            $this->deleteImage($post);
        }

        $post->delete();

        return response()->json([
            'message' => 'Success delete post',
            'status' => 'oke',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => 'error',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required|min:4|max:255',
            'body' => 'required|min:4',
            'image' => 'required|image|file',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $this->deleteImage($post);
            $name = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move('uploads', $name);
            $data['image'] = url('uploads' . '/' . $name);
        }

        $post->update($data);

        return response()->json([
            'message' => 'Success update post',
            'status' => 'oke',
            'post' => $post
        ], 200);
    }
}
