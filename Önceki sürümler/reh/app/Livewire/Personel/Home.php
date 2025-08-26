<?php

namespace App\Livewire\Personel;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('livewire.personel.home', [
        ])->layout('layouts.personel');// importante kullanıcı edit viewı burdan alıyor
    }
}
