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
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        $videos = $query->paginate(18);
        $categories = Video::getCategories();
        $totalVideos = Video::active()->count();
        
        return view('videos.index', compact('videos', 'categories', 'totalVideos'));
    }

    public function show(Video $video): View
    {
        // Increment views
        $video->incrementViews();
        
        // Get related videos by tags (if video has tags)
        $relatedVideos = collect();
        
        if (is_array($video->tags) && count($video->tags) > 0) {
            // Find videos that share at least one tag
            $relatedVideos = Video::active()
                ->where('id', '!=', $video->id)
                ->where(function ($query) use ($video) {
                    foreach ($video->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                })
                ->ordered()
                ->take(6)
                ->get();
        }
        
        // If no videos with similar tags, fallback to same category
        if ($relatedVideos->isEmpty() && $video->category) {
            $relatedVideos = Video::active()
                ->where('id', '!=', $video->id)
                ->byCategory($video->category)
                ->ordered()
                ->take(6)
                ->get();
        }
        
        // If still no videos, just get latest videos
        if ($relatedVideos->isEmpty()) {
            $relatedVideos = Video::active()
                ->where('id', '!=', $video->id)
                ->ordered()
                ->take(6)
                ->get();
        }
        
        return view('videos.show', compact('video', 'relatedVideos'));
    }
}