<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        Project::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return 'Project added successfully!';
    }

    function list(){
        return Project::get();
    }

    function delete ($id)
    {
        $result = Project::where('id', $id)->delete();
    
        if ($result) {
            return response()->json(["result" => "Product has been deleted"], 200);
        } else {
            return response()->json(["result" => "Operation failed"], 400);
        }
    }
// API to get single product
    function getproduct($id)
{
    return Project::find($id);
}

// update api
function updateproduct($id,Request $request){
    // return $id;
    // return $request->input();
    $product = Project::find($id);
    
    // Check if the product exists
    if ($product) {
        // Update the product's attributes
        $product->name = $request->name;
        $product->price = $request->price;
        
        // Save the changes
        $product->save();
        
        return response()->json(['message' => 'Product updated successfully'], 200);
    } else {
        return response()->json(['message' => 'Product not found'], 404);
    }
}
function search($key){
    return Project::where('name','LIKE', "%$key%")->get();;
}



}


