<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Objects;
use JsonException;

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
        try {
            // $arr['mykey'] = 'this is mmy val';
            // list($key, $value) = $arr;
            $data = $request->json()->all();
            // foreach ($data as $key => $value) {
            //     var_dump($key . ':' . $value);
            // }
            // $key = key($data);
            // var_dump($key);
            // var_dump($data[$key]);
            // die('test');
    
            if (!$request->isJson()  || empty($data)) {
                return response()->json([
                    'message' => 'Invalid JSON format'
                ], Response::HTTP_BAD_REQUEST);
                
            // } else {
            //     return response()->json([
            //         "message" => "Thank you!"
            //     ], 200);

            }

            // taking all the json objects passed to the api. but we can also just take first element through $key = key($data);
            foreach ($data as $key => $value) {
                try {        
                    // validate $key, as string and max 255 chars. We'll send 400 error for failure. Although it's possible we can skip through
                    if (!is_string($key) || strlen($key) > 255) {
                        return response([
                            'message' => 'Invalid request details.'
                        ], Response::HTTP_BAD_REQUEST);
                    }

                    // TODO: check for blob encoding
                    //      encode $value blob

                    Objects::create([
                        'key' => $key,
                        'value' => $value,
                        'is_binary' => false,
                        'timestamp' => time()
                    ]);
                } catch (JsonException $e) {
                    return response()->json([
                        'message' => 'Invalid data type for key: ' . $key
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            return response()->json(['message' => 'Stored successfully'], Response::HTTP_CREATED);


        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    
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
