<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
            $products = Product::latest()->select('id', 'name', 'description', 'image', 'category_id', 'sub_category_id')->with(['category', 'subCategory'])->get();
            
            if (count($products) > 0) {
                $responseData['status'] = 200;
                $responseData['message'] = 'Product list get successfully.';
                $responseData['data'] = $products;
            } else {
                $responseData['status'] = 200;
                $responseData['message'] = 'No Products found.';
                $responseData['data'] = $products;
            }
                
            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::emergency('Product controller index Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            
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
                'category_id' => 'required|integer',
                'sub_category_id' => 'integer',
                'description' => 'required|string',
                'image' => 'required|image',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 200;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //Create new instance of Product db
            $products = new Product();
            $products->name = $request->name;
            $products->description = $request->description;
            $products->category_id = $request->category_id;
            $products->sub_category_id = $request->sub_category_id;

            $destinationPath = public_path(config('constants.products'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = 'product-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $products->image = $fileName;
            }
            $products->save();

            $responseData['status'] = 201;
            $responseData['message'] = 'Product store successfully.';
            $responseData['data'] = $products;

            return $this->commonResponse($responseData, 201);
        } catch (\Exception $e) {
            Log::info('Product store exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
            $responseData['message'] = 'Product Id is required.';
            return $this->commonResponse($responseData, 406);
        }

		try {
            $products = Product::find($id);
            $responseData['status'] = 200;
            $responseData['message'] = 'Product get successful.';
            $responseData['data'] = $products;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;
            Log::info('Product controller show Exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
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
        
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'sub_category_id' => 'integer',
                'description' => 'required|string',
                'image' => 'image',
            ]);
            if ($validator->fails()) {
                $responseData['status'] = 400;
                $responseData['errors'] = $validator->errors()->toArray();
                return $this->commonResponse($responseData, 400);
            }

            //Create new instance of Product db
            $products = Product::find($id);
            $products->name = $request->name;
            $products->description = $request->description;
            $products->category_id = $request->category_id;
            $products->sub_category_id = $request->sub_category_id;

            $destinationPath = public_path(config('constants.products'));

            if ($request->hasFile('image')) {
				if ($products->image != null && file_exists($destinationPath.$products->image)) {
                    unlink($destinationPath.$products->image);
                }

                $image = $request->file('image');
                $fileName = 'product-img-'.date('YmdHsi').rand(10, 99).'.'.$image->getClientOriginalExtension();
                $image->move($destinationPath, $fileName);
                $products->image = $fileName;
            }
            $products->save();

            $responseData['status'] = 200;
            $responseData['message'] = 'Product has been updated successfully.';
            $responseData['data'] = $products;

            return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('Product update exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());
            $responseData['status'] = 500;
            $responseData['errors'] = $e->getMessage();
            $code = 500;

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
            $products = Product::find($id);
            // delete Product image from storage
            if ($products->image != null && file_exists(public_path(config('constants.products').$products->image))) {
                unlink(public_path(config('constants.products').$products->image));
            }

            $products->delete();
			$responseData['status'] = 200;
			$responseData['message'] = 'Product has been deleted successfully';

			return $this->commonResponse($responseData, 200);
        } catch (\Exception $e) {
            Log::info('Delete Product catch an exception:: Message:: '.$e->getMessage().' line:: '.$e->getLine().' Code:: '.$e->getCode().' file:: '.$e->getFile());

            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
