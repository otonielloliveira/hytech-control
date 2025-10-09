<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Video::active()->ordered();
        
        // Filter by category if provided
        if ($request->filled('categoria')) {
            $query->byCategory($request->categoria);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $videos = $query->paginate(12);
        $categories = Video::getCategories();
        
        return view('videos.index', compact('videos', 'categories'));
    }

    public function show(Video $video): View
    {
        // Increment views
        $video->incrementViews();
        
        // Get related videos (same category)
        $relatedVideos = Video::active()
            ->where('id', '!=', $video->id)
            ->when($video->category, function ($query, $category) {
                return $query->byCategory($category);
            })
            ->ordered()
            ->take(6)
            ->get();
        
        return view('videos.show', compact('video', 'relatedVideos'));
    }
}