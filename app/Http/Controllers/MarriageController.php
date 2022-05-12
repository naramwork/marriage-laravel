<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\MarriageRequest;
use App\Models\Message;
use App\Models\User;
use App\Traits\Firebase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MarriageController extends Controller
{

    use Firebase;

    // add Marriage Request sent from one user of the app to the database 
    // and send notification to both sender and recipient
    public function store(Request $request)
    {
        $statue = '';
        $content  = '';
        $attr = $request->validate([
            'sender_id' => 'required|numeric',
            'recipient_id' => 'required|numeric',

        ]);

        $message = Message::where('sender_id', $request->sender_id)
            ->where('recipient_id', $request->recipient_id)->get();
        // don't allow user to send marriage request before sending one message at least
        if ($message->isEmpty()) {
            $statue = 'error';
            $content = 'عليك إرسال رسالة واحدة على الأقل قبل إرسال طلب زواج';
            return ['status' => $statue, 'content' => $content];
        }

        //check to see if there is one marriage request with no response before sending one more
        $oldMarriage = MarriageRequest::where('sender_id', $request->sender_id)
            ->where('recipient_id', $request->recipient_id)
            ->where('status', 'في الإنتظار')->first();
        if ($oldMarriage) {
            $statue = 'error';
            $content = 'هنالك طلب مسبق من دون رد الرجاء الإنتظار قبل إرسال طلب اخر.';
            return ['status' => $statue, 'content' => $content];
        }


        $marriage =  MarriageRequest::create($attr);

        if ($marriage) {
            $statue = 'success';
            $content = 'لقد تم إرسال طلب الزواج بنجاح';
            return ['status' => $statue, 'content' => $content];
            $recipientFcm = $marriage->recipient->profile->fire_base_token;
            if (gettype($recipientFcm) == 'string') {
                $recipientFcm = explode(',', $recipientFcm);
            }
            $this->sendFcmNotification($recipientFcm, 'لديك طلب زواج');
        } else {
            $statue = 'error';
            $content = 'حدث خطأ ما الرجاء المحاولة لاحقا';
            return ['status' => $statue, 'content' => $content];
        }
    }


    // update the request when an admin change its status from control panel
    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $marriageRequest = MarriageRequest::find($request->id);

        if (!$marriageRequest) {
            throw ValidationException::withMessages([
                'no-marriage-request' => ['يوجد خطأ ما الرجاء المحاولة لاحقا'],
            ]);
        }

        $senderFcm = $marriageRequest->sender->profile->fire_base_token;
        if (gettype($senderFcm) == 'string') {
            $senderFcm = explode(',', $senderFcm);
        }

        if ($request->status == 'إلغاء') {

            $marriageRequest->status = $request->status;
            $this->sendFcmNotification($senderFcm, ' لقد تم ' . $request->status . ' طلب الزواج ');
            return $marriageRequest->save();
        } else {
            $marriageRequest->status = $request->status;
            $this->sendFcmNotification($senderFcm, ' لقد تم ' . $request->status . ' طلب الزواج الخاص بك');
            return $marriageRequest->save();
        }
    }


    // retrieve user marriage requests 
    public function show($id)
    {
        $senderMessage = MarriageRequest::where('sender_id', $id)->get()->groupBy('recipient_id');
        $recipientMessage = MarriageRequest::where('recipient_id', $id)->get()->groupBy('sender_id');
        return collect(['send' => $senderMessage, 'receive' => $recipientMessage]);
    }


    // get random list of users tho show on the home page of the app 
    // if the user is male show female users and ......
    public function get_random(String $gender)
    {
        return CustomerProfile::with(['user'])->inRandomOrder()->Where('gender', '!=', $gender)->paginate(10);
    }


    //get all users to show all result when user not logged in
    public function get_all_user()
    {
        return CustomerProfile::with(['user'])->inRandomOrder()->paginate(10);
    }

    public function sendFcmNotification(array $tokenList, String $body)
    {

        $extraNotificationData = [
            "body" => $body,
            "title" => "طلب زواج"
        ];

        $fcmNotification = [
            'registration_ids' => $tokenList, //multple token array
            // 'to'        => "/topics/messaging", //single token

            'notification' => $extraNotificationData
        ];

        return $this->firebaseNotification($fcmNotification);
    }
}
