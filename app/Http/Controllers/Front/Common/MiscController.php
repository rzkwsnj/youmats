<?php

namespace App\Http\Controllers\Front\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\InquireRequest;
use App\Models\Contact;
use App\Models\Inquire;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MiscController extends Controller
{
    public function changeCurrency(Request $request) {
        try {
            setCurrency($request->code);
        } catch (\Exception $e) {
            $output['status'] = 0;
        }
        $output['status'] = 1;
        echo json_encode($output);
        return;
    }

    public function changeCity(Request $request) {
        $data = $this->validate($request, [
            'city_id' => [REQUIRED_INTEGER_VALIDATION, ...['exists:cities,id']]
        ]);
        setCity($data['city_id']);
        return response()->json(['message' => 'Done']);
    }

    public function subscribeRequest(Request $request) {
        $data = $this->validate($request, [
            'email' => 'email|unique:subscribers,email'
        ], [
            'email.unique' => 'You already subscribed.'
        ]);

        try {
            $subscriber = Subscriber::create($data);
            if($subscriber) {
                $message = 'You subscribe successfully.';
//                Email::sendEmailForms($contact, $message);
            }
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => true,
            'message' => $message
        ]);
    }

    public function inquireRequest(InquireRequest $request) {
        $data = $request->validated();

        try {
            $data['phone'] = $data['quotation_phone'];
            $contact = Inquire::create($data);
            if(isset($request->file)) {
                $contact->addMedia($request->file)->toMediaCollection(INQUIRE_PATH);
            }
            if($contact) {
                $message = 'You request submitted successfully.';
//                Email::sendEmailForms($contact, $message);
            }
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function introduce($type) {
        if($type == 'individual' || $type == 'company') {
            Session::put('userType', $type);
        }
        return response()->json([
            'status' => true
        ]);
    }
}
