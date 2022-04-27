<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use Illuminate\Http\Request;

class AppInfoController extends Controller
{

    // Ad bar in the home screen of the flutter app
    public function getAppAd()
    {
        return AppInfo::where('type', 'app_ad')->first();
    }

   
    // All the info that displayed in "آلية الزواج" page in the app
    public function getMarriageExplain()
    {
        return AppInfo::where('type', 'marriage_explain')->first();
    }

    // All the info that displayed in "اتصل بنا" page in the app
    public function getCallUs()
    {
        return AppInfo::where('type', 'call_us')->get();
    }
}
