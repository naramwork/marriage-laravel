<?php

namespace App\Http\Controllers;


use App\Models\AppInfo;
use App\Models\Notification;

class ContentApiController extends Controller
{

   

    // Get Marriage App Notification From the Database
    public function getMarriageNotification()
    {
        return Notification::where('type', '=',0)->get();
    }

    // All the info that displayed in "حول التطبيق" page in the app
    public function getMarriageAboutUs()
    {
        return AppInfo::where('type', 'm_about_us')->first();
    }
}
