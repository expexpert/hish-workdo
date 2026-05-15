<?php

namespace Modules\LandingPage\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Utility;
use Modules\LandingPage\Entities\LandingPageSetting;


class LandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('landingpage::landingpage.topbar');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('landingpage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $data = [
            "topbar_status" => $request->topbar_status ? $request->topbar_status : "off",
            "feature_status" => $request->feature_status ? $request->feature_status : "off",
            "topbar_notification_msg" =>  $request->topbar_notification_msg,
        ];

        foreach($data as $key => $value){

            LandingPageSetting::updateOrCreate(['name' =>  $key],['value' => $value]);
        }

        return redirect()->back()->with(['success'=> 'Topbar setting update successfully']);

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('landingpage::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('landingpage::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'contact-name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Utility::getSMTPDetailsNew(1);

            Mail::to('contact@simply-compta.com')->send(new \App\Mail\ContactFormMail($request->all()));
            return redirect()->back()->with('success', 'Votre demande a été envoyée avec succès !');
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['mail_error' => 'Une erreur est survenue lors de l\'envoi de l\'email : ' . $e->getMessage()]);
        }
    }
}
