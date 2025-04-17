<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    public function show()
    {
        try {
//            $redis = Redis::connection();
            $name = Redis::get('name');
            return response()->json($name);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            Redis::set('name', $request->get('name'));
            return response()->json($request->get('name'));
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
