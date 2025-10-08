<?php

namespace App\View\Composers;

use App\Models\Category;
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

        $view->with('categories', $categories);
    }
}