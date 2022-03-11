<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
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
            $banner = Banner::latest()->select('id', 'title', 'description', 'image', 'link')->get();
            
            if (count($banner) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Banner list get successful.';
                $responseData['data'] = $banner;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No banner found.';
                $responseData['data'] = $banner;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Banner controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            
            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse $responseData
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
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'sometimes|image|max:10240',
                'link' => 'string|max:255',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //create new instance of banner db
            $banner = new Banner();
            $banner->title = $request->title;
            $banner->description = $request->description;
            $banner->link = $request->link;

            $destinationPath = public_path(config('constants.banner_url'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'Banner-Img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $banner->image = $fileName;
            }
            $banner->save();

            $responseData['status'] = 201;
            $responseData['message'] = 'Banner store successfully.';
            $responseData['data'] = $banner;

            return $this->commonResponse($responseData, 201);
        } catch (\Exception $e) {
            Log::info('Banner store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Banner Id is required.';
            return $this->commonResponse($responseData, 406);
        }
        try {
            $bannerData = Banner::find($id);
            $responseData['status'] = 200;
            $responseData['message'] = 'Banner get successful.';
            $responseData['data'] = $bannerData;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Banner controller show Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Banner Id is required.';
            return $this->commonResponse($responseData, 406);
        }
		Log::info("Banner update request:: ".json_encode($request->all()));
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'sometimes|image|max:10240',
				'link' => 'string|max:255',
            ]);
            
            if ($validator->fails()) {
                $responseData['status'] = 406;
                $responseData['errors'] = $validator->errors()->toArray();

                return $this->commonResponse($responseData, 406);
            }

            $bannerData = Banner::find($id);

            $fileName = null;
            $destinationPath = public_path(config('constants.banner_url'));
            if ($request->hasFile('image')) {
                // remove already added image
				if ($bannerData->image != null && file_exists($destinationPath.$bannerData->image)) {
                    unlink($destinationPath.$bannerData->image);
                }

                $image = $request->file('image');
                $fileName = 'banner-Img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $bannerData->image = $fileName;
            }
            $bannerData->title = $request->title;
            $bannerData->description = $request->description;
            $bannerData->link = $request->link;

            $bannerData->update();
            $responseData['status'] = 200;
            $responseData['message'] = 'Banner has been updated successfully';
        
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Banner controller update Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            return $this->commonResponse($responseData, $code);
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
            $responseData['message'] = 'Banner Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $bannerData = Banner::find($id);
			if(!is_null($bannerData)) {
				// delete banner image from storage
				if ($bannerData->image != null && file_exists(public_path(config('constants.banner_url').$bannerData->image))) {
					unlink(public_path(config('constants.banner_url').$bannerData->image));
				}
				$bannerData->delete();
				$responseData['status'] = 200;
				$responseData['message'] = 'Banner has been deleted successfully';
			} else {
				$responseData['message'] = 'No Banner Found.';
				$responseData['status'] = 204;
			}
			

			return $this->commonResponse($responseData, 200);

        } catch (\Exception $e) {
            Log::info('delete banner catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
