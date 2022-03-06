<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pioneer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PioneerController extends Controller
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
            $pioneer = Pioneer::latest()->select('id', 'name', 'description', 'image')->get();
            
            if (count($pioneer) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Pioneer list get successfully.';
                $responseData['data'] = $pioneer;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No Pioneer found.';
                $responseData['data'] = $pioneer;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Pioneer controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile(). ' Code :: '.$code);
            
            return $this->commonResponse($responseData, 500);
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
                'description' => 'required|string',
                'image' => 'required|image',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //create new instance of pioneer db
            $pioneer = new Pioneer();
            $pioneer->name = $request->name;
            $pioneer->description = $request->description;

            $destinationPath = public_path(config('constants.pioneer_url'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'pioneer-Img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $pioneer->image = $fileName;
            }
            $pioneer->save();

            $responseData['status'] = 201;
            $responseData['message'] = 'Pioneer store successfully.';
            $responseData['data'] = $pioneer;

            return $this->commonResponse($responseData, 201);
        } catch (\Exception $e) {
            Log::info('Pioneer store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Pioneer Id is required.';
            return $this->commonResponse($responseData, 406);
        }
        try {
            $pioneerData = Pioneer::find($id);
            $responseData['status'] = 200;
            $responseData['message'] = 'Pioneer get successful.';
            $responseData['data'] = $pioneerData;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Pioneer controller show Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        if (!$id) {
            $responseData['status'] = 406;
            $responseData['message'] = 'Pioneer Id is required.';
            return $this->commonResponse($responseData, 406);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'image',
            ]);
            
            if ($validator->fails()) {
                $responseData['status'] = 406;
                $responseData['errors'] = $validator->errors()->toArray();

                return $this->commonResponse($responseData, 406);
            }

            $pioneerData = Pioneer::find($id);

            $fileName = null;
            $destinationPath = public_path(config('constants.pioneer_url'));
            if ($request->hasFile('image')) {
                // remove already added image
                if (file_exists($destinationPath.$pioneerData->image)) {
                    unlink($destinationPath.$pioneerData->image);
                }

                $image = $request->file('image');
                $fileName = 'Pioneer-Img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $pioneerData->image = $fileName;
            }
            $pioneerData->name = $request->name;
            $pioneerData->description = $request->description;

            $pioneerData->update();
            $responseData['status'] = 200;
            $responseData['message'] = 'Pioneer has been updated successfully';
        
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Pioneer controller update Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            return $this->commonResponse($responseData, 500);
        }
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
            $responseData['message'] = 'Pioneer Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $pioneerData = Pioneer::find($id);
			if(!is_null($pioneerData)) {
				// delete Pioneer image from storage
				if (file_exists(public_path(config('constants.pioneer_url').$pioneerData->image))) {
					unlink(public_path(config('constants.pioneer_url').$pioneerData->image));
				}
				$pioneerData->delete();
				$responseData['status'] = 200;
				$responseData['message'] = 'Pioneer has been deleted successfully';
			} else {
				$responseData['message'] = 'No Pioneer Found.';
				$responseData['status'] = 200;
			}
			

			return $this->commonResponse($responseData, 200);

        } catch (\Exception $e) {
            Log::info('delete Pioneer catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
