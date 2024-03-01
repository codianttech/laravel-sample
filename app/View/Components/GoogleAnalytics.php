<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;

class GoogleAnalytics extends Component
{
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $id = getSetting('app.google_analytics');
        $this->id = ($id) ? $id : null;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.google-analytics');
    }
}
