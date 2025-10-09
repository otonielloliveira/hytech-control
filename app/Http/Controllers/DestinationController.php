<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Banner;
use App\Models\Category;
use App\Models\BlogConfig;
use App\Services\SidebarService;

class DestinationController extends Controller
{
    protected function getBaseData()
    {
        return [
            'config' => BlogConfig::current(),
            'banners' => Banner::where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::withCount(['posts' => function ($query) {
                $query->where('is_published', true);
            }])->get(),
        ];
    }

    public function artigos(Request $request)
    {
        $posts = Post::artigos()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.artigos', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Artigos',
            'description' => 'Confira nossos artigos mais relevantes e informativos'
        ]));
    }

    public function peticoes(Request $request)
    {
        $posts = Post::peticoes()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.peticoes', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Petições',
            'description' => 'Participe das nossas petições e faça a diferença'
        ]));
    }

    public function ultimasNoticias(Request $request)
    {
        $posts = Post::ultimasNoticias()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.ultimas-noticias', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Últimas Notícias',
            'description' => 'Fique por dentro das últimas notícias e acontecimentos'
        ]));
    }

    public function noticiasMundiais(Request $request)
    {
        $posts = Post::noticiasMundiais()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.noticias-mundiais', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Notícias Mundiais',
            'description' => 'Acompanhe as principais notícias do mundo'
        ]));
    }

    public function noticiasNacionais(Request $request)
    {
        $posts = Post::noticiasNacionais()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.noticias-nacionais', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Notícias Nacionais',
            'description' => 'As principais notícias do Brasil'
        ]));
    }

    public function noticiasRegionais(Request $request)
    {
        $posts = Post::noticiasRegionais()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.noticias-regionais', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Notícias Regionais',
            'description' => 'Fique informado sobre as notícias da sua região'
        ]));
    }

    public function politica(Request $request)
    {
        $posts = Post::politica()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.politica', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Política',
            'description' => 'Análises e notícias sobre o cenário político'
        ]));
    }

    public function economia(Request $request)
    {
        $posts = Post::economia()
            ->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(12);

        return view('blog.destinations.economia', array_merge($this->getBaseData(), [
            'posts' => $posts,
            'title' => 'Economia',
            'description' => 'Informações e análises sobre economia e mercado'
        ]));
    }
}