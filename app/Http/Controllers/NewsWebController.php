<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Guardian\GuardianAPI;


class NewsWebController extends Controller
{
    //
    function index() 
    {
        $guardian_api = config('app.guardian_key');
        $api = new GuardianAPI($guardian_api);
        $response = $api->tags()
        ->setQuery("apple")
        ->setSection("technology")
        ->setShowReferences("all")
        ->fetch();
        $results = $response->response->results;
        // dd($response->response->results);  
        return view('display',compact('results'));
    }
}
