<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use App\Traits\Firebase;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;
use Spatie\ImageOptimizer\OptimizerChain;

class NotificationModal extends ModalComponent
{
    use Firebase;
    use WithFileUploads;



    public String $title = '';
    public String $body = '';
    public $image;
    public String $app_type = '';

    // method listeners
    protected $listeners = [
        'sendNotification' => 'sendNotification',
    ];

    protected $rules = [
        'title' => 'required',
        'body' => 'required',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|600',
    ];
    public function mount($app){
        $this->app_type = $app;
    }

    public function render()
    {
        return view('livewire.notification-modal');
    }


    public function sendNotification()
    {
        

        if($this->image){
            $imageName = time().'.'.$this->image->extension(); 
            $this->image->storeAs("public/images",$imageName);
        
            $extraNotificationData = [
                "body" => $this->body,
                "title" => $this->title,
                'image' => url('/') .'/storage/images/'.$imageName,
                'mutable_content' => true
            ];
        }else{
            $extraNotificationData = [
                "body" => $this->body,
                "title" => $this->title,          
            ];
    
        }
        $type = 0;
            $topic = '/topics/marriage';
        
        $newNotification = new Notification();
        $newNotification->body = $this->body;
        $newNotification->title = $this->title;
        $newNotification->type = $type;
        $newNotification->save();
        $saved = $newNotification->save();
       
       
        $fcmNotification = [
            // 'registration_ids' => $tokenList, //multple token array
            'to'        => $topic, //single token

            'notification' => $extraNotificationData,
           
              
        ];
        $response = $this->firebaseNotification($fcmNotification);
        if (isset($response['message_id']) && $saved) {
            $this->closeModal();
            $this->emitTo('bottom-alert', 'customMessage', 'تم العملية بنجاح', 'green');
            $this->emitTo('control-page', 'setShowAlertModal');
        } else {
            $this->closeModal();

            $this->emitTo('bottom-alert', 'customMessage', 'حدث خطأ ما الرجاء المحاولة لاحقا', 'red');
            $this->emitTo('control-page', 'setShowAlertModal');
        }
    }
}
