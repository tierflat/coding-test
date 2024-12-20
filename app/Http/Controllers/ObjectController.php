<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objects;

class ObjectController extends Controller
{
    public function get_all_records() {
        try {
            $objects = Objects::all();
            return response()->json($objects);
            // return response()->json([
            //     "message" => "Thank you!"
            // ], 200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request) {
        $validateData = $request->validate(['key' => 'required|string|max:255']);
        
    }

    public function show($id) {
        $object = Objects::find($id);
        if(!empty($object)) {
            return response()->json($object);
        } else {
            return response()->json([
                "message" => "Object not found!"
            ], 404);
        }

    }
}
