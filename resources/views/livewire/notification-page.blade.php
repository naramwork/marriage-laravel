<div class="sm:p-4 md:p-8 lg:w-11/12 text-center mx-auto ">

    <x-sub-header>
        الإشعارات
    </x-sub-header>

    <div class="pt-2 bg-white overflow-x-auto shadow-xl sm:rounded-lg">
      
        @livewire('notification-table')
    </div>
</div>

<div x-data="{ show: @entangle('showAlertModel')}" x-show="show" x-effect="
    if(show){
        setTimeout(() => { show=false }, 2000);
    }
    ">
    <livewire:bottom-alert>


</div>
