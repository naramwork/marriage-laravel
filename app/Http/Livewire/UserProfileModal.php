<?php

namespace App\Http\Livewire;

use App\Models\Message;
use App\Models\User;

use LivewireUI\Modal\ModalComponent;

class UserProfileModal extends ModalComponent
{

    public User $user;
    //public Message $message;

    public function mount(int $id)
    {
        $this->user = User::find($id);
    }


    public function render()
    {
        return view('livewire.user-profile-modal');
    }

    public static function modalMaxWidth(): string
    {
        return 'md';
    }
}
