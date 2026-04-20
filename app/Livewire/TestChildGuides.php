<?php

namespace App\Livewire;

use Livewire\Component;

class TestChildGuides extends Component
{
    public $childGuides = ['H00001'];
    public $totalPieces = 3;
    public $link_child_later = false;

    public function addChildGuide($guide)
    {
        $this->childGuides[] = $guide;
    }

    public function render()
    {
        return view('livewire.test-child-guides');
    }
}
