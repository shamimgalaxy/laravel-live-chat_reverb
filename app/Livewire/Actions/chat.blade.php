<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Chat extends Component
{
    public $users;
    public $selectedUser;

    public function mount()
    {
        $this->users = User::where('id', '!=', Auth::id())->get();
        
        // Default to the first user's ID
        $this->selectedUser = $this->users->first();
    }

  public function selectUser($id)
{
    
    $this->selectedUser = User::find($id);
}

   public function render(){

  return view('livewire.chat')->layout('layouts.app');
}
}