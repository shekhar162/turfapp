<?php

namespace App\Http\Controllers\Api\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponder;
    protected $user = null;
    protected $product = null;

    // list all product
    public function index(Request $request){
        $this->user = $request->user();
        return $this->responseWithData(
            ["products" => $this->user->products()->with('price')->get()]
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
            return $this->responseWithError($validator->errors()->all(), $request->all());
        }
        $this->user = $request->user();

        $this->product = new Product();
        $this->product->name = $request->name;
        $this->product->mobileNumber = $this->user->mobileNumber;
        //$this->product->category_id = $request->categoryId;
        $this->product->closedDays = $request->closedDays;
        $this->product->closedHrs = $request->closedHrs;
        $this->product->suitableFor = $request->suitableFor;
        $this->product->otherFacility = $request->otherFacility;
        $this->product->status = $request->status;
        $this->product->save();

        $this->product->price()->create([
            "price" => $request->price,
            "currency" =>$request->currency
        ]);
        
        return $this->responseWithData(
            $this->product,
            'New product created successfully!'
        );  
    }

    public function show(Request $request){
        if(!$this->product = $request->user()->products()->find($request->id)){
            return $this->responseWithError("No record found.");
        }

        $this->product->load('price');
        return $this->responseWithData(
            $this->product
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
            return $this->responseWithError($validator->errors()->all(), $request->all());
        }
        $this->user = $request->user();

        $this->product = Product::find($request->id);
        if(!$this->product){
            return $this->responseWithError("No record found.",$request->all());
        }
        
        $this->product->name = $request->name;
        $this->product->mobileNumber = $this->user->mobileNumber;
        //$this->product->category_id = $request->categoryId;
        $this->product->closedDays = $request->closedDays;
        $this->product->closedHrs = $request->closedHrs;
        $this->product->suitableFor = $request->suitableFor;
        $this->product->otherFacility = $request->otherFacility;
        $this->product->status = $request->status;
        $this->product->update();

        $this->product->price()->update(
            [
                "price" => $request->price,
                "currency" =>$request->currency
            ]
        );
        
        return $this->responseWithData(
            $this->product->refresh()->load(['price']),
            'Product updated successfully!'
        );  
    }

    public function delete(Request $request){
        $this->product = Product::find($request->id);
        if(!$this->product){
            return $this->responseWithError("No record found.",$request->all());
        }

        $this->product->price()->delete();
        $this->product->delete();

        $this->user = $request->user();
        return $this->responseWithData(
            $this->user->products,
            'Product deleted successfully'
        );
    }

}
