<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class NotificationTable extends LivewireDatatable
{
    public $model = Notification::class;

   
    public function columns()
    {
        return [
            NumberColumn::name('id')->alignCenter()->label('id'),
            Column::name('title')->searchable()->alignCenter()->unsortable()->label('العنوان'),
            Column::name('body')->searchable()->alignCenter()->unsortable()->label('المحتوى'),
            DateColumn::name('created_at')->alignCenter()->label('تاريخ الإرسال'),
            Column::callback(['id'], function ($id) {

                return view('actions.notification-action', [ 'id' => $id]);
            })->unsortable()
        ];
    }

    public function notificationDelete($id)
    {
        $notification = Notification::find($id);
        $notification->delete();

    }


    public function cellClasses($row, $column)
    {

        return 'align-middle overflow-hidden text-lg text-center py-4  leading-10 ';
    }

    public function rowClasses($row, $loop)
    {
        if ($loop->even) {
            return 'bg-gray-50';
        }
    }
}