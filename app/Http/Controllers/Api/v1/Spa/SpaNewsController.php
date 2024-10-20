<?php

namespace App\Http\Controllers\Api\v1\Spa;

use App\Http\Controllers\Controller;
use App\Models\NewsModel\NewsModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpaNewsController extends Controller
{
    public function newsInfo()
    {
        $id = request()->id;
        $latest = request()->latest;
        if($id){
            $course = NewsModel::find($id);
            return response()->json($course, Response::HTTP_OK);
        }
        if($latest){
            $course = NewsModel::latest()->take(10)->get();
            return response()->json($course, Response::HTTP_OK);
        }
        $course = NewsModel::latest()->paginate(50);
        return response()->json($course, Response::HTTP_OK);
    }
}
