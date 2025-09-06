<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SideNavLink extends Component
{
    public $active;
    public $sidebarOpen;

    public function __construct($active = false, $sidebarOpen = true)
    {
        $this->active = $active;
        $this->sidebarOpen = $sidebarOpen;
    }

    public function render()
    {
        return view('components.side-nav-link');
    }
}
