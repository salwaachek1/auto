<?php

namespace App\Http\Controllers;
use App\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
  public function index()
    {     
        
        $activities = Activity::get();    
        return view('admin.activitieslist')->with(['activities' => $activities]);
    }

}
