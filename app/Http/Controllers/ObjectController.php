<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Objects;

class ObjectController extends Controller
{
    public function index() {
        $objects = Objects::all();
        return response()->json($objects);
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
