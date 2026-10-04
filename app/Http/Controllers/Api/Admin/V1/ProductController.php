<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponder;

    // list all product
    public function index(Request $request){
        $user = $request->user();
        return $this->responseWithData(
            ["products" => $user->products()->with('price')->get()]
        );
    }

    // create new product.
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1|max:255',
            'price'=>'required'
        ]);

        // return if validation failed
        if ($validator->fails()) {
            return $this->responseWithError($validator->errors()->all());
        }
        $user = $request->user();

        $product = new Product();
        $product->name = $request->name;
        $product->mobileNumber = $user->mobileNumber;
        //$product->category_id = $request->categoryId;
        $product->closedDays = $request->closedDays;
        $product->closedHrs = $request->closedHrs;
        $product->suitableFor = $request->suitableFor;
        $product->otherFacility = $request->otherFacility;
        $product->status = $request->status;
        $product->save();

        $productPrice = $product->price()->create([
            "price" => $request->price,
            "currency" =>$request->currency
        ]);
        
        return $this->responseWithData(
            $product,
            'New product created successfully!'
        );  
    }

    public function show(Request $request){
        if(!$product = Product::find($request->id)){
            return $this->responseWithError("No record found.");
        }

        if ($product->mobileNumber != $request->user()->mobileNumber) {
            return $this->responseWithError("Unauthorized access.");
        }
            
        $product->load('price');
        return $this->responseWithData(
            $product
        );
        
    }

    // create new product.
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:1|max:255',
            'price'=> 'required',
        ]);

        // return if validation failed
        if ($validator->fails()) {
            return $this->responseWithError($validator->errors()->all());
        }
        $user = $request->user();

        $product = Product::find($request->id);
        if(!$product){
            return $this->responseWithError("No record found.");
        }
        
        $product->name = $request->name;
        $product->mobileNumber = $user->mobileNumber;
        //$product->category_id = $request->categoryId;
        $product->closedDays = $request->closedDays;
        $product->closedHrs = $request->closedHrs;
        $product->suitableFor = $request->suitableFor;
        $product->otherFacility = $request->otherFacility;
        $product->status = $request->status;
        $product->update();

        $productPrice = $product->price()->update(
            [
                "price" => $request->price,
                "currency" =>$request->currency
            ]
        );
        
        return $this->responseWithData(
            $product->refresh()->load(['price']),
            'Product updated successfully!'
        );  
    }

    public function delete(Request $request){
        $product = Product::find($request->id);
        if(!$product){
            return $this->responseWithError("No record found.");
        }

        $product->price()->delete();
        $product->delete();

        $user = $request->user();
        return $this->responseWithData(
            $user->products,
            'Product deleted successfully'
        );
    }

}
