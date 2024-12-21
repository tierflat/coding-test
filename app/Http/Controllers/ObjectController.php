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

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Responnse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request) {
        try {
            $data = $request->json()->all();
    
            if (!$request->isJson()  || empty($data)) {
                return response()->json([
                    'message' => 'Invalid JSON format'
                ], Response::HTTP_BAD_REQUEST);
            }

            foreach ($data as $key => $value) {
                try {        
                    // validate $key, as string and max 255 chars. We'll send 400 error for failure. Although it's possible we can skip through
                    if (!is_string($key) || strlen($key) > 255) {
                        return response([
                            'message' => 'Invalid request details.'
                        ], Response::HTTP_BAD_REQUEST);
                    }

                    $isBinary = $this->isBinary($value);

                    /*
                    Assuming that we're storing small blob data, we'll store it as base64 encoded.
                    In case we're getting very large blob like videos, we should save the blob as a file and 
                    store the path in database
                    */ 
                    Objects::create([
                        'key' => $key,
                        'value' => $isBinary ? base64_encode($value) : $value,
                        'is_binary' => $isBinary,
                        'timestamp' => time()
                    ]);
                    return response()->json(['message' => 'Stored successfully'], Response::HTTP_CREATED);

                } catch (JsonException $e) {
                    return response()->json([
                        'success' => false,
                        'error' => $e->getMessage()
                    ], Response::HTTP_BAD_REQUEST);
                }
            }



        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Responnse::HTTP_INTERNAL_SERVER_ERROR);
        }
    
    }

    public function show($key) {
        try {
            // check if timestamp is passed
            $timestamp = request('timestamp');
            if($timestamp && !is_numeric($timestamp)) {
                return response()->json([
                    'message' => 'Invalid timestamp'
                ], Response::HTTP_BAD_REQUEST);
            }

            if($timestamp) {
                $object = Objects::getByTimestamp($key, $timestamp);
            } else {
                $object = Objects::getLatestValue($key);
            }
            if(!$object) {
                return response()->json([
                    'message' => 'Object not found'
                ], Response::HTTP_NOT_FOUND);
            }
            if($object->is_binary) {
                $object->value = base64_decode($object->value);
                return response($object->value)
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $key . '"');
            }

            return response()->json([
                $object->value,
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Responnse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    private function isBinary($value) {
        if(!is_string($value)) {
            return !mb_check_encoding($value, 'UTF-8') ||
            preg_match('/[^\x20-\x7E\t\r\n]/', $value) ||
            substr($value, 0, 2) === "\x1f\x8b";
        }
        return false;
    }

    
}
