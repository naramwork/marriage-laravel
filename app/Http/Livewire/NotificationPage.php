<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationPage extends Component
{
    public  $showAlertModel = false;

    protected $listeners = ['showAlert', 'setShowAlertModal' , 'notificationDelete'];

    public function render()
    {
        return view('livewire.notification-page');
    }

   

    /**
     * showAlert show bottom alert with custom message
     *
     * @param  mixed $type
     * @return void
     */
    public function showAlert($type)
    {


        $this->emit('changeType', $type);
        $this->emitSelf('setShowAlertModal');
    }
    public function setShowAlertModal()
    {
        $this->showAlertModel = true;
    }
}
