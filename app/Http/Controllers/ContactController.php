<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        try {
            $contact = Contact::latest()->select('id', 'name', 'email', 'subject', 'message')->get();
            
            if (count($contact) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Contact list get successful.';
                $responseData['data'] = $contact;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No contact found.';
                $responseData['data'] = $contact;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Contact controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            
            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //create new instance of contact db
            $contact = new Contact();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->subject = $request->subject;
            $contact->message = $request->message;

            $contact->save();

            $responseData['status'] = 201;
            $responseData['message'] = 'Contact store successfully.';
            $responseData['data'] = $contact;

            return $this->commonResponse($responseData, 201);
        } catch (\Exception $e) {
            Log::info('Contact store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;

            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
		
		if (!$id) {
            $responseData['status'] = 406;
            $responseData['message'] = 'Contact Id is required.';
            return $this->commonResponse($responseData, 406);
        }

        try {
            $contactData = Contact::find($id);
            $responseData['status'] = 200;
            $responseData['message'] = 'Contact get successful.';
            $responseData['data'] = $contactData;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Contact controller show Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        if (!$id) {
            $responseData['status'] = 406;
            $responseData['message'] = 'Contact Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $contactData = Contact::find($id);
			if(!is_null($contactData)) {
				$contactData->delete();
				
				$responseData['status'] = 200;
				$responseData['message'] = 'Contact has been deleted successfully';
			} else {
				$responseData['message'] = 'No Contact Found.';
				$responseData['status'] = 204;
			}
			

			return $this->commonResponse($responseData, 200);

        } catch (\Exception $e) {
            Log::info('delete contact catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
