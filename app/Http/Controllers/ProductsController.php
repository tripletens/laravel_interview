<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Products;
use App\Jobs\ProductFactory;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class ProductsController extends Controller
{
    // this function creates a product
    public function create_product(Request $request){

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required'
        ]);

        if($validator->fails()){
            $data = [
                'message' => "An Error Occured",
                'data' => [],
                'error' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $product = Products::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'category_id' => $request->get('category_id')
        ]);
    
        $data = [
            'message' => 'Product Created Successfully',
            'data' => [],
            'error' => $validator->errors()
        ];
        return response()->json($data,201);
    }

    public function edit_product(Request $request){

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required',
        ]);

        // category_id is optional

        if($validator->fails()){
            $data = [
                'message' => "Validation Error",
                'data' => [],
                'error' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $product_id = $request->get('product_id');

        $product = Products::find($product_id);

        if($product == null){
            $data = [
                'message' => "Product doesnt Exist",
                'data' => [],
                'error' => ['Product not found']
            ];
            return response()->json($data, 400);
        }

        $update = $product->update([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'category_id' ? $request->get('category_id') : $product->category_id,
        ]);

        if(!$update){
            $data = [
                'message' => 'An Error Occured',
                'data' => [],
                'error' => ['Sorry could not update product record']
            ];
            return response()->json($data,400);
        }
            
        $data = [
            'message' => 'Product updated Successfully',
            'data' => [],
            'error' => $validator->errors()
        ];

        return response()->json($data,201);
    }

    public function delete_product(Request $request){

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|max:255',
        ]);

        if($validator->fails()){
            $data = [
                'message' => "An Error Occured",
                'data' => [],
                'error' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $product_id = $request->get('product_id');

        $product = Products::find($product_id);

        if($product == null){
            $data = [
                'message' => "Product doesnt Exist",
                'data' => [],
                'error' => ['Product not found']
            ];
            return response()->json($data, 400);
        }

        $delete = $product->delete();

        if(!$delete){
            $data = [
                'message' => 'An Error Occured',
                'data' => [],
                'error' => ['Sorry could not delete product record']
            ];
            return response()->json($data,400);
        }
            
        $data = [
            'message' => 'product deleted successfully',
            'data' => [],
            'error' => $validator->errors()
        ];

        return response()->json($data,201);
    }
    public function create_fifty_products(){
        
        $dispatch = ProductFactory::dispatch()
            ->delay(now()->addMinutes(2));

        if(!$dispatch){
            $data = [
                'message' => 'An Error Occured',
                'error' => ['Sorry could not create 50 products']
            ];
            return response()->json($data,400);
        }
        
        $data = [
            'message' => '50 Products created successfully',
            'data' => [],
            'error' => []
        ];

        return response()->json($data,201);
    }

    // exports products in excel 
    public function export_products(){

        $random = Str::random(40);
        $filename = 'Products_'. $random. '.xlsx';
        $store = Excel::store(new ProductsExport, $filename,'public');

        if(!$store){
            $data = [
                'message' => 'An Error Occured',
                'data' => [],
                'error' => ['Sorry could not export products']
            ];
            return response()->json($data,400);
        }

        $url = Storage::url('public'.$filename);
        
        $data = [
            'message' => 'Products exported successfully',
            'data' => [
                'link' => $url
            ],
            'error' => []
        ];

        return response()->json($data,201);
    }
    
}
