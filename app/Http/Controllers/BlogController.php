<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Banner;
use App\Models\BlogConfig;
use App\Models\Newsletter;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $config = BlogConfig::current();
        $banners = Banner::active()->ordered()->get();
        $featuredPosts = Post::published()
            ->featured()
            ->with(['category', 'user', 'tags'])
            ->take(3)
            ->get();
        $latestPosts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->take(6)
            ->get();
        
        return view('blog.index', compact(
            'config',
            'banners', 
            'featuredPosts', 
            'latestPosts'
        ));
    }

    public function show(Post $post)
    {
        if (!$post->isPublished()) {
            abort(404);
        }

        $post->incrementViews();
        
        // Carregar as tags do post
        $post->load('tags', 'category', 'user');
        
        $config = BlogConfig::current();
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['category', 'user'])
            ->take(3)
            ->get();
        
        $comments = $post->approvedComments()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('blog.show', compact('config', 'post', 'relatedPosts', 'comments'));
    }

    public function category(Category $category)
    {
        $config = BlogConfig::current();
        $posts = $category->publishedPosts()
            ->with(['user', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $otherCategories = Category::active()
            ->where('id', '!=', $category->id)
            ->withCount('publishedPosts')
            ->take(8)
            ->get();

        return view('blog.category', compact('config', 'category', 'posts', 'otherCategories'));
    }

    public function tag(Request $request, string $tagSlug)
    {
        $config = BlogConfig::current();
        
        // Buscar a tag pelo slug
        $tag = Tag::where('slug', $tagSlug)->firstOrFail();
        
        $posts = $tag->publishedPosts()
            ->with(['user', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $popularTags = Tag::withCount([
                'posts' => function ($query) {
                    $query->where('status', 'published')
                          ->where('published_at', '<=', now());
                }
            ])
            ->orderBy('posts_count', 'desc')
            ->take(15)
            ->get();

        return view('blog.tag', compact('config', 'tag', 'posts', 'popularTags'));
    }

    public function newsletterSubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:blog_newsletters,email',
            'name' => 'nullable|string|max:255',
        ]);

        Newsletter::create([
            'email' => $request->email,
            'name' => $request->name,
            'status' => 'active', // ou 'pending' se quiser confirmação
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Inscrição realizada com sucesso!');
    }

    public function newsletterUnsubscribe(string $token)
    {
        $subscriber = Newsletter::where('token', $token)->firstOrFail();
        $subscriber->deactivate();

        return view('blog.newsletter.unsubscribed');
    }

    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => 'required_unless:user_id,!=,null|string|max:255',
            'author_email' => 'required_unless:user_id,!=,null|email|max:255',
        ]);

        $commentData = [
            'post_id' => $post->id,
            'content' => $request->content,
            'status' => 'pending', // moderação
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        if (Auth::check()) {
            $commentData['user_id'] = Auth::id();
        } else {
            $commentData['author_name'] = $request->author_name;
            $commentData['author_email'] = $request->author_email;
        }

        Comment::create($commentData);

        return back()->with('success', 'Comentário enviado! Será analisado pela moderação.');
    }
}
