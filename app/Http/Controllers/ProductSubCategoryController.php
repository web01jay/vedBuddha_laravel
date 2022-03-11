<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        try {
			if($request->parentId > 0) {
				$subCategory = ProductSubCategory::where('parent_id', $request->parentId)->latest()->select('id', 'name', 'image')->get();
			} else {
				$subCategory = ProductSubCategory::latest()->select('id', 'name', 'image')->get();
			}
            
            if (count($subCategory) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Product Sub Categories list get successfully.';
                $responseData['data'] = $subCategory;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No Product Sub Categories found.';
                $responseData['data'] = $subCategory;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Product Sub Category controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            
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
				'parent_id' => 'required|integer',
                'image' => 'image',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //Create new instance of Product Category db
            $productSubCategory = new ProductSubCategory();
            $productSubCategory->parent_id = $request->parent_id;
            $productSubCategory->name = $request->name;

            $destinationPath = public_path(config('constants.productSubCategory_url'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'product-sub-category-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $productSubCategory->image = $fileName;
            }
            $productSubCategory->save();

            $responseData['status'] = 200;
            $responseData['message'] = 'Product Sub Category store successfully.';
            $responseData['data'] = $productSubCategory;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('Product Sub Category store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;

            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductSubCategory  $productSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		// dd($productSubCategory);
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
            $productSubCategory = ProductSubCategory::find($id);

            $responseData['status'] = 200;
			if(is_null($productSubCategory)) {
				$responseData['message'] = 'No product sub category found.';
			} else {
				$responseData['message'] = 'Product Sub Category get successful.';
			}
            $responseData['data'] = $productSubCategory;

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
		$responseData = array();
        $responseData['status'] = 0;
        $responseData['message'] = '';
        $responseData['data'] = [];
        $responseData['errors'] = [];
        
        if (!$id) {
            $responseData['status'] = 406;
            $responseData['message'] = 'Product Sub Category Id is required.';
            return $this->commonResponse($responseData, 406);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
				'parent_id' => 'required|integer',
                'image' => 'image',
            ]);
            
            if ($validator->fails()) {
                $responseData['status'] = 406;
                $responseData['errors'] = $validator->errors()->toArray();

                return $this->commonResponse($responseData, 406);
            }

            $productSubCategory = ProductSubCategory::find($id);

            $fileName = null;
            $destinationPath = public_path(config('constants.productSubCategory_url'));
            if ($request->hasFile('image')) {
                // remove current image
                if ($productSubCategory->image != null && file_exists($destinationPath.$productSubCategory->image)) {
                    unlink($destinationPath.$productSubCategory->image);
                }

                $image = $request->file('image');
                $fileName = 'product-sub-category-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $productSubCategory->image = $fileName;
            }
            $productSubCategory->name = $request->name;
			$productSubCategory->parent_id = $request->parent_id;
            $productSubCategory->update();
            
            $responseData['status'] = 200;
            $responseData['message'] = 'Product Sub Category has been updated successfully';
        
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Product Sub Category controller update Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            return $this->commonResponse($responseData, $code);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
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
            $responseData['message'] = 'Product Sub Category Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $productSubCategory = productSubCategory::find($id);
            // delete productSubCategory image from storage
            if ($productSubCategory->image != null && file_exists(public_path(config('constants.productSubCategory_url').$productSubCategory->image))) {
                unlink(public_path(config('constants.productSubCategory_url').$productSubCategory->image));
            }

            $productSubCategory->delete();
			$responseData['status'] = 200;
			$responseData['message'] = 'Product Sub Category has been deleted successfully';

			return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('delete Product Sub Category catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
