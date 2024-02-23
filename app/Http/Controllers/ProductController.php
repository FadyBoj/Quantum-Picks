<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Tag\ImageTag;

use function Laravel\Prompts\error;
use function PHPSTORM_META\map;

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

    public function addProducts(Request $request)
    {
        $data = Storage::disk('local')->get('data.json');
        ini_set('max_execution_time', 3600); //60 minutes

        foreach (json_decode($data) as $item)
        {
            $uploadedImages = [];

            foreach($item->images as $index =>$productImage)
            {
                if($index < 2)
                {
                    $downloadedImage = file_get_contents($productImage);
                    $stringArray = explode('.',$productImage);
                    $extension = end($stringArray);
                    $filename = "image." . $extension;
                    Storage::disk('local')->put($filename, $downloadedImage);
                    $uploadImage = (new UploadApi())->upload(base_path('storage/app/'.$filename));
                    $uploadedImages[] = $uploadImage;
                }
             
            }
            
            $newProduct = Product::create([
                "title" => $item->title,
                "description" => "This is the product description",
                "price" => $item->price,
                "quantity" => mt_rand(20,100),
                "category" => $item->category,
            ]);

            foreach($uploadedImages as $item)
            {
                Image::create([
                    "image" => $item['url'],
                    "product_id" => $newProduct->id,
                    "public_id" => $item['public_id']
                ]);
            }

        }

        return response()->json(["msg" => "Successfully added all the products",200]);
    }

    // public function addOffers(Request $request)
    // {
    //     $products = Product::all();
    //     $ids = array_map(function($product){
    //         return $product->id;
    //     },json_decode($products));
    //     $productsLength = count($products);

    //     $randomIds = [];
    //     $loopCount = 20;

    //     for($i = 0; $i< $loopCount; $i++)
    //     {
    //         $newRandomId = mt_rand($ids[0],end($ids));
    //         if(in_array($newRandomId,$randomIds))
    //         {
    //             error_log("Duplicate !");
    //             $loopCount++;
    //             continue;
    //         }
    //         $randomIds[] = $newRandomId;
    //     }

    //     foreach($randomIds as $productId)
    //     {
    //        $product = Product::find($productId);
    //        $product->offer = true;
    //        $product->offerPrice = ((((double)$product->price) / 2) + 1000);
    //        $product->save();
    //     }

    //     return response()->json(["ids" => $randomIds]);
    // }
}
