<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Services\SidebarService;
use App\Services\YouTubeService;
use Illuminate\View\View;

class BlogComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $categories = Category::active()
            ->ordered()
            ->withCount('publishedPosts')
            ->get();

        // Sidebar data - individual queries por enquanto
        $polls = \App\Models\Poll::active()->get();
        $lectures = \App\Models\Lecture::active()->ordered()->limit(5)->get();
        $downloads = \App\Models\Download::active()->byPriority()->limit(5)->get();
        $books = \App\Models\Book::getFeaturedBooks(3);
        $hangouts = \App\Models\Hangout::getUpcomingHangouts(3);
        
        // YouTube data
        try {
            $youtubeService = new YouTubeService();
            $config = \App\Models\BlogConfig::current();
            $youtubeData = $youtubeService->getChannelData($config->youtube_channel_url ?: '');
        } catch (\Exception $e) {
            $youtubeData = null;
        }

        $view->with([
            'categories' => $categories,
            'polls' => $polls,
            'lectures' => $lectures,
            'downloads' => $downloads,
            'books' => $books,
            'hangouts' => $hangouts,
            'youtubeData' => $youtubeData,
        ]);
    }
}