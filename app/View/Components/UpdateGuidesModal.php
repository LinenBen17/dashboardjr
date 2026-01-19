<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UpdateGuidesModal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $id = 'updateGuides',
        public ?string $heading = null,
        public array $paymentMethods = [],
        public array $productos = []
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.update-guides-modal');
    }
}
