<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
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
            $productCategory = ProductCategory::latest()->select('id', 'name', 'image')->get();
            
            if (count($productCategory) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Product Categories list get successfully.';
                $responseData['data'] = $productCategory;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No Product Categories found.';
                $responseData['data'] = $productCategory;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Product Category controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            
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
                'image' => 'required|image',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //Create new instance of Product Category db
            $productCategory = new ProductCategory();
            $productCategory->name = $request->name;

            $destinationPath = public_path(config('constants.productCategory_url'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'product-category-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $productCategory->image = $fileName;
            }
            $productCategory->save();

            $responseData['status'] = 200;
            $responseData['message'] = 'Product Category store successfully.';
            $responseData['data'] = $productCategory;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('Product Category store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Product Category Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $productCategory = ProductCategory::find($id);
            $responseData['status'] = 200;
            $responseData['message'] = 'Product Category get successful.';
            $responseData['data'] = $productCategory;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Product Category controller show Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            $responseData['message'] = 'Product Category Id is required.';
            return $this->commonResponse($responseData, 406);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'image',
            ]);
            
            if ($validator->fails()) {
                $responseData['status'] = 406;
                $responseData['errors'] = $validator->errors()->toArray();

                return $this->commonResponse($responseData, 406);
            }

            $productCategory = ProductCategory::find($id);

            $fileName = null;
            $destinationPath = public_path(config('constants.productCategory_url'));
            if ($request->hasFile('image')) {
                // remove current image
                if (file_exists($destinationPath.$productCategory->image)) {
                    unlink($destinationPath.$productCategory->image);
                }

                $image = $request->file('image');
                $fileName = 'product-category-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $productCategory->image = $fileName;
            }
            $productCategory->name = $request->name;
            
            $productCategory->update();
            $responseData['status'] = 200;
            $responseData['message'] = 'Product Category has been updated successfully.';
        
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Product Category controller update Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Product Category Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $productCategoryData = ProductCategory::find($id);
            // delete productCategory image from storage
            if (file_exists(public_path(config('constants.productCategory_url').$productCategoryData->image))) {
                unlink(public_path(config('constants.productCategory_url').$productCategoryData->image));
            }

            $productCategoryData->delete();
			$responseData['status'] = 200;
			$responseData['message'] = 'Product Category has been deleted successfully';

			return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('delete Product Category catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
