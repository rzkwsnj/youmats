<?php

namespace App\Http\Controllers\Front\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ContactRequest;
use App\Models\Contact;
use App\Models\FAQ;
use App\Models\StaticPage;

class PageController extends Controller
{
    public function faqs()
    {
        $data['FAQs'] = FAQ::orderBy('sort')->get(['id', 'question', 'answer']);

        return view('front.pages.faq')->with($data);
    }

    public function page($slug)
    {
        $page = StaticPage::where('slug', $slug)->first();

        return view('front.pages.index')->with(compact('page'));
    }

    public function contactUs()
    {
        return view('front.pages.contact');
    }

    public function contactUsRequest(ContactRequest $request)
    {
        $data = $request->validated();

        try {
            $contact = Contact::create($data);
            if ($contact) {
                $message = __('messages.contact_added');
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
}
