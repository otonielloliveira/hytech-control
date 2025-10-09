<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Banner;
use App\Models\BlogConfig;
use App\Models\Newsletter;
use App\Models\Comment;
use App\Models\PetitionSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $config = BlogConfig::current();
        $banners = Banner::active()->ordered()->get();
        
        // Posts em destaque (is_featured = true)
        $featuredPosts = Post::published()
            ->featured()
            ->with(['category', 'user', 'tags'])
            ->take(3)
            ->get();
        
        // Posts por destino para diferentes seções
        $artigosPosts = Post::published()
            ->artigos()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(6)
            ->get();
            
        $peticoesPosts = Post::published()
            ->peticoes()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $ultimasNoticiasPosts = Post::published()
            ->ultimasNoticias()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(6)
            ->get();
            
        $noticiasMundiaisPosts = Post::published()
            ->noticiasMundiais()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $noticiasNacionaisPosts = Post::published()
            ->noticiasNacionais()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $noticiasRegionaisPosts = Post::published()
            ->noticiasRegionais()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $politicaPosts = Post::published()
            ->politica()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $economiaPosts = Post::published()
            ->economia()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->take(4)
            ->get();
        
        // Posts mais recentes (fallback para quando não há posts suficientes por destino)
        $latestPosts = Post::published()
            ->with(['category', 'user', 'tags'])
            ->latest('published_at')
            ->take(6)
            ->get();
        
        // Categorias
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])->get();
        
        return view('blog.index', compact(
            'config',
            'banners', 
            'featuredPosts', 
            'latestPosts',
            'artigosPosts',
            'peticoesPosts',
            'ultimasNoticiasPosts',
            'noticiasMundiaisPosts',
            'noticiasNacionaisPosts',
            'noticiasRegionaisPosts',
            'politicaPosts',
            'economiaPosts',
            'categories'
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

    public function storePetitionSignature(Request $request, Post $post)
    {
        // Verificar se o post é uma petição
        if ($post->destination !== 'peticoes') {
            abort(404, 'Esta funcionalidade está disponível apenas para petições.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tel_whatsapp' => 'required|string|max:20',
            'estado' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'link_facebook' => 'nullable|url|max:500',
            'link_instagram' => 'nullable|url|max:500',
            'observacao' => 'nullable|string|max:1000',
        ]);

        // Verificar se o email já assinou esta petição
        $existingSignature = PetitionSignature::where('post_id', $post->id)
            ->where('email', $request->email)
            ->first();

        if ($existingSignature) {
            return back()->with('error', 'Você já assinou esta petição com este e-mail.');
        }

        PetitionSignature::create([
            'post_id' => $post->id,
            'nome' => $request->nome,
            'email' => $request->email,
            'tel_whatsapp' => $request->tel_whatsapp,
            'estado' => $request->estado,
            'cidade' => $request->cidade,
            'link_facebook' => $request->link_facebook,
            'link_instagram' => $request->link_instagram,
            'observacao' => $request->observacao,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'signed_at' => now(),
        ]);

        return back()->with('success', 'Obrigado! Sua assinatura foi registrada com sucesso na petição.');
    }
}
