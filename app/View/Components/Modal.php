<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $modalId;
    public $title;
    public $content;

    // Constructor to initialize the props
    public function __construct($modalId, $title, $content)
    {
        $this->modalId = $modalId;
        $this->title = $title;
        $this->content = $content;
    }

    public function render()
    {
        return view('components.modal');
    }
}
