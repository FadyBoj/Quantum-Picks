<?php

use Cloudinary\Configuration\Configuration;
          
Configuration::instance([
  'cloud' => [
    'cloud_name' => env('CLOUDINARY_NAME'), 
    'api_key' => env('CLOUDINARY_KEY'), 
    'api_secret' => env('CLOUDINARY_SECRET')],
  'url' => [
    'secure' => true]]);