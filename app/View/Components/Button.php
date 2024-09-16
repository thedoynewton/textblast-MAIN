<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $type;
    public $color;

    public function __construct($type = 'button', $color = 'red')
    {
        $this->type = $type;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.button');
    }
}
