<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Categories;

class CategoryController extends Controller
{
    // this function creates a category
    public function create_category(Request $request){

        $validator = Validator::make($request->all(), [
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

        $categories = Categories::create([
            'name' => $request->get('name'),
            'description' => $request->get('description')
        ]);
    
        $data = [
            'message' => 'Category Created Successfully',
            'data' => [],
            'error' => $validator->errors()
        ];
        return response()->json($data,201);
    }

    public function edit_category(Request $request){

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

        $category_id = $request->get('category_id');

        $category = Categories::find($category_id);

        $update = $category->update([
            'name' => $request->get('name'),
            'description' => $request->get('description')
        ]);

        if(!$update){
            $data = [
                'message' => 'An Error Occured',
                'data' => [],
                'error' => ['Sorry could not update Category record']
            ];
            return response()->json($data,400);
        }
            
        $data = [
            'message' => 'Category updated Successfully',
            'data' => [],
            'error' => $validator->errors()
        ];

        return response()->json($data,201);
    }

    public function delete_category(Request $request){

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|max:255',
        ]);

        if($validator->fails()){
            $data = [
                'message' => "An Error Occured",
                'data' => [],
                'error' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $category_id = $request->get('category_id');

        $category = Categories::find($category_id);

        $delete = $category->delete();

        if(!$delete){
            $data = [
                'message' => 'An Error Occured',
                'data' => [],
                'error' => ['Sorry could not delete Category record']
            ];
            return response()->json($data,400);
        }
            
        $data = [
            'message' => 'Category deleted successfully',
            'data' => [],
            'error' => $validator->errors()
        ];

        return response()->json($data,201);
    }
}
