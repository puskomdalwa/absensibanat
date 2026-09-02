<?php
namespace App\Http\Controllers\Home;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('home.landing-page.index');
    }

    public function getData(Request $request)
    {
        $data = User::where('departemen_id', $request->departemen_id)->limit(8)->get();
        return view('home.landing-page.data', compact('data'));
    }
}
