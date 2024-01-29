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

use function PHPUnit\Framework\returnSelf;

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
                $path =  (new UploadApi())->upload($image->path());
                $pathes[] = $path;
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
                    "image" => $path['url'],
                    "product_id" => $newProduct->id,
                    "public_id" => $path['public_id']
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

    // Modify product
    public function modifyProduct(Request $request)
    {
        try
        {
            $data = $request->all();
            $queryObject = [];

            if(key_exists('title',$data))
            $queryObject['title'] = $data['title'];

            if(key_exists('description',$data))
            $queryObject['description'] = $data['description'];

            if(key_exists('price',$data))
            $queryObject['price'] = $data['price'];

            if(key_exists('quantity',$data))
            $queryObject['quantity'] = $data['quantity'];

            if(key_exists('category',$data))
            $queryObject['category'] = $data['category'];

            if($request->hasFile('images'))
            {
                //deleting old images 
               $previousImages = Product::find($data['id'])->images()->get();

               foreach($previousImages as $singleImage)
               {
             
                (new UploadApi())->destroy($singleImage->public_id);
                
                Image::where('id',$singleImage->id)->delete();

               }

               //Storing new images

               $images = $request->file("images");
               $pathes = [];

               foreach($images as $singleImage)
               {
                $path =  (new UploadApi())->upload($singleImage->path());
                $imageUrl= $path['url'];

                Image::create([
                    "image" => $imageUrl,
                    "product_id" => $data['id'],
                    "public_id" => $path['public_id']
                ]);

               }

            }

            if(!count($queryObject) > 0)
            throw new CustomException("Please update 1 field at least",400);

            Product::where('id',$data['id'])->update($queryObject);

            return response()->json(["msg" =>"Successfully updated product"],200);
        }
        catch(Exception $e)
        {
            throw new CustomException($e->getMessage(),400);
        }
    }
}
