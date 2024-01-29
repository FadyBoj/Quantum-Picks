<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

//Models
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;

class AdminController extends Controller
{
    //Add new product 
    public function addProduct(Request $request){
        try
        {
            $data = $request->all();
            $images = $request->file("images");
            $pathes = [];
            foreach($images as $image)
            {
                $path =  (new UploadApi())->upload($image->path(), 
                ["public_id" => $image->getClientOriginalName()]);
                $pathes[] = $path['url'];
            }

            $newProduct = Product::create([
                "title" => $data['title'],
                "description" => $data['description'],
                "price" => $data['price'],
                "quantity" => $data["quantity"],
                "category" => $data["category"]
            ]);

            foreach($pathes as $path)
            {
                Image::create([
                    "image" => $path,
                    "product_id" => $newProduct->id
                ]);
            }

            return response()->json(["msg" => "Product added successfully"],200);
    
    }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
    

    }

    //Add Category
    public function addCategory(Request $request)
    {
        try
        {
            $request->validateWithBag('Category',[
                "title" => "required|unique:categories"
            ],[
               "title.unique" => "A product with the same title already exist." 
            ]);

            Category::create([
                "title" => $request->title,
            ]);

            return response()->json(["msg" => "Added category successfully."],200); 

        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
       
    }
}
