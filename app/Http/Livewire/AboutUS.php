<?php

namespace App\Http\Livewire;

use App\Models\AppInfo;
use LivewireUI\Modal\ModalComponent;

class AboutUS extends ModalComponent
{

    public String $name = '';
    public String $content = '';
    public String $app_type = '';



    protected $listeners = [
        'addToDataBase' => 'addToDataBase',
    ];

    protected $rules = [
        'title' => 'required',
        'body' => 'required',
    ];

    public function mount(String $app)
    {
        $this->app_type = $app;

        if($this->app_type ===  config('enums.app_type')['marriage']){
            $aboutUs = AppInfo::where('type', 'm_about_us')->first();
        }else{
            $aboutUs = AppInfo::where('type', 'about_us')->first();

        }
        if ($aboutUs) {
            $this->name = $aboutUs->name;
            $this->content = $aboutUs->content;
        }
    }

    public function render()
    {
        return view('livewire.about-u-s');
    }


    public function addToDataBase()
    {
        if($this->app_type ===  config('enums.app_type')['marriage']){
            $aboutUs = AppInfo::where('type', 'm_about_us')->first();
            $type = 'm_about_us';
        }else{
            $aboutUs = AppInfo::where('type', 'about_us')->first();
            $type = 'about_us';


        }
        if ($aboutUs) {
            $aboutUs->name = $this->name;
            $aboutUs->content = $this->content;
        } else {
            $aboutUs = new AppInfo();
            $aboutUs->name = $this->name;
            $aboutUs->type = $type;
            $aboutUs->content = $this->content;
        }

        if ($aboutUs->save()) {
            $this->closeModal();
            $this->emitTo('bottom-alert', 'customMessage', 'تمت العملية بنجاح', 'green');
            $this->emitTo('control-page', 'setShowAlertModal');
        } else {
            $this->closeModal();

            $this->emitTo('bottom-alert', 'customMessage', 'حدث خطأ ما الرجاء المحاولة لاحقا', 'red');
            $this->emitTo('control-page', 'setShowAlertModal');
        }
    }
}
