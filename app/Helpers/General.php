<?php

// any function i use it in here i can call it all the project
// because i use this file and put it in array i make it and it is called files in composer.json

use Illuminate\Support\Facades\Config;

function get_languages(){
    return \App\Models\Language::active() -> Selection() -> get();
}

function get_default_lang(){
    return Config::get('app.locale');
}

function uploadImage($folder, $image)
{
    $image->store('/', $folder);
    $filename = $image->hashName();
    $path = 'images/' . $folder . '/' . $filename;
    return $path;
}
