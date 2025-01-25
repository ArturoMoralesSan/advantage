<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DB;
class DashboardController extends Controller
{
    public function index()
    {
        

            return view('admin.dashboard');   
             
    }
}
