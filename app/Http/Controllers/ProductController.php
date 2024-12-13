<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;  // Assuming you have a Product model

class ProductController extends Controller
{
    public function addProduct(Request $req)
    {
        // Store the file in the 'projects' folder in the public disk
        // $filePath = $req->file('file_path')->store('projects', 'public');

        // Create a new product record
        $product = new Product();
        $product->name = $req->input('name');
        // $product->file_path = $filePath;
        $product->price = $req->input('price');
        $product->save();

        // Return a response with a success message
        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product
        ], 201);
    }
}
