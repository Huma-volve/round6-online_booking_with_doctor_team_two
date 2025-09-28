<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\pages;
class PageController extends Controller
{
    //
    public function privacyPolicy()
    {
        $page = pages::where('type', 'privacy-policy')->firstOrFail();
        return response()->json($page);
    }

    public function termsAndConditions()
    {
        $page = pages::where('type', 'terms-and-conditions')->firstOrFail();
        return response()->json($page);
    }
}
