<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use PulkitJalan\Google\Facades\Google;
use Revolution\Google\Sheets\Facades\Sheets;

Route::get('/', function () {
    return view('welcome');
});


Route::get('fetch', function(){
    Sheets::setService(Google::make('sheets'));
    Sheets::spreadsheet('18k42WM4EIs5RVDGlQG6HvMPylFUtB4gRed5zHhX6HqE');

    $data = Sheets::sheet('Necesidades')->all();

    if(request()->has('pretty'))
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);

    return $data;
});

Route::get('parsekml',function(){
    $fileShelters = file_get_contents('https://www.google.com/maps/d/u/0/kml?mid=1UsEmeSqMGW1fIgbzPN4jKY027WA&lid=aD6k7tbiI4E&forcekml=1');

    $geo = geoPHP::load($fileShelters, 'kml');

    dd(geoPHP::geometryReduce($geo));
});