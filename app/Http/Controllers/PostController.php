<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::query()
            ->whereNotNull('published_at')
            ->with('user:id,name,email')
            ->latest('published_at')->active()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $posts,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'posts.create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        // 4-3: Validate & Create
        $post = $request->user()->posts()->create($request->validated());

        return response()->json([
            'status' => true,
            'code' => 201,
            'message' => 'Post Created',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        if ($post->is_draft || $post->published_at > now()) {
            abort(404);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $post->load('user'),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): string
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        $post->update($request->validated());

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Post Updated',
            'data' => $post,
            200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json([
            'status' => true,
            'code' => 204,
            'message' => 'Post Deleted',
        ], 204);
    }
}
