<?php

namespace App\Http\Controllers\api\ServiceProvider\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ProviderContact;

class ContactController extends Controller
{
    public function __construct(private ProviderContact $contact){} 

    public function view(Request $request){
        $contact = $this->contact
        ->where('provider_id', $request->user()->provider_id)
        ->get(); 

        return response()->json([ 
            'contact' => $contact, 
        ]);
    }
    
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'watts_status' => ['required', 'boolean'],
            'phone_status' => ['required', 'boolean'],
            'website_status' => ['required', 'boolean'],
            'instagram_status' => ['required', 'boolean'],
            'watts' => ['required'],
            'phone' => ['required'],
            'website' => ['required'],
            'instagram' => ['required'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        
        $contact = $this->contact
        ->where('provider_id', $request->user()->provider_id)
        ->orderByDesc('id')
        ->first();
        $adminRequest = $validator->validated();
        $adminRequest['provider_id'] = $request->user()->provider_id;
        if (empty($contact)) {
            $this->contact
            ->create($adminRequest);
        } 
        else {
            $contact->update($adminRequest);
        }
        
        return response()->json([
            'success' => 'You add data success',
        ]);
    }
}
