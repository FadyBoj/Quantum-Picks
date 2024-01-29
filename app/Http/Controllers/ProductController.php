<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $products = Product::with('images')->get();
        return response()->json($products,200);
    }

    public function getSingleProduct(string $id)
    {
        $product = Product::find($id);
        if(!$product)
        throw new CustomException("No product found with id $id");
    
        return response()->json($product,200);
    }
}
