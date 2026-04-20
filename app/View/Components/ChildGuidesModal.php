<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChildGuidesModal extends Component
{
    public function __construct(
        public string $id = 'childGuides',
        public string $heading = 'Enlace de Guías Hijas',
        public string $label = 'Guía Hija',
        public string $placeholder = 'Escanee las guías hijas',
        public string $helperText = 'Estas guías hijas estarán enlazadas al envío.',
        public string $counterLabel = 'Guías Hijas Enlazadas',
        public int $count = 0,
        public bool $linkLater = false,
        public string $toggleLabel = 'Enlazar Después',
        public string $buttonText = 'Enlazar',
        public string $action = 'confirmChilds',

        public array $childGuides = [],
    ) {}

    public function render()
    {
        return view('components.child-guides-modal');
    }
}
