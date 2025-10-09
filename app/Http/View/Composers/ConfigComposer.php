<?php

namespace App\Http\View\Composers;

use App\Models\BlogConfig;
use Illuminate\View\View;

class ConfigComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('config', BlogConfig::current());
    }
}