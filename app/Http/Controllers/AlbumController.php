<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function index(): View
    {
        $albums = Album::active()
            ->ordered()
            ->with(['photos' => function ($query) {
                $query->take(4);
            }])
            ->paginate(12);
        
        return view('albums.index', compact('albums'));
    }

    public function show(Album $album): View
    {
        // Load photos with pagination
        $photos = $album->photos()
            ->ordered()
            ->paginate(20);
        
        return view('albums.show', compact('album', 'photos'));
    }
}