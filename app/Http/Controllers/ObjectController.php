<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Objects;
use JsonException;

class ObjectController extends Controller
{
    /**
     * Retrieves all records from the database.
     */
    public function get_all_records() {
        try {
            $objects = Objects::all();
            return response()->json($objects);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Stores a new object in the database.
     * 
     * @param Request $request
     */
    public function store(Request $request) {
        try {
            $data = $request->json()->all();
    
            if (!$request->isJson()  || empty($data)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid JSON format'
                ], Response::HTTP_BAD_REQUEST);
            }

            foreach ($data as $key => $value) {
                try {        
                    // validate $key, as string and max 255 chars. We'll send 400 error for failure. Although it's possible we can skip through
                    if (!is_string($key) || strlen($key) > 255) {
                        return response([
                            'success' => false,
                            'error' => 'Invalid request details.'
                        ], Response::HTTP_BAD_REQUEST);
                    }

                    $isBinary = false;
                    if($this->isBase64($value)) {
                        $decoded = base64_decode($value, true);
                        $isBinary = $this->isBinary($decoded);
                        if (!$isBinary) $value = $decoded;
                    }
                    /*
                    Assuming that we're storing small blob data, we'll store it as base64 encoded.
                    In case we're getting very large blob like videos, we should save the blob as a file and 
                    store the path in database
                    */ 
                    Objects::create([
                        'key' => $key,
                        'value' => $value,
                        'is_binary' => $isBinary,
                        'timestamp' => time()
                    ]);
                    return response()->json(['success' => true, 'message' => 'Stored successfully'], Response::HTTP_CREATED);

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
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
    }

    /**
     * Retrieves the latest value from the database based on the given $key.
     * If a timestamp is provided, it retrieves the value before the given timestamp.
     * 
     * @param string $key
     */
    public function show($key) {
        try {
            // check if timestamp is passed
            $timestamp = request('timestamp');
            if($timestamp && !is_numeric($timestamp)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid timestamp'
                ], Response::HTTP_BAD_REQUEST);
            }

            if($timestamp) {
                $object = Objects::getByTimestamp($key, $timestamp);
            } else {
                $object = Objects::getLatestValue($key);
            }
            if(!$object) {
                return response()->json([
                    'success' => false,
                    'error' => 'Object not found'
                ], Response::HTTP_NOT_FOUND);
            }
            if($object->is_binary) {
                $object->value = base64_decode($object->value);
                return response($object->value)
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $key . '"');
            }
            return response($object->value);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Checks if the given string is binary
     * @param mixed $string
     */
    private function isBinary($string) {
        $binarySignatures = [
            "\x89\x50\x4E\x47" => 'PNG',
            "\xFF\xD8\xFF"     => 'JPEG',
            "\x47\x49\x46"     => 'GIF',
            "\x50\x4B\x03\x04" => 'ZIP/DOCX/XLSX',
            "\x25\x50\x44\x46" => 'PDF',
        ];
        
        foreach ($binarySignatures as $signature => $format) {
            if (strpos($string, $signature) === 0) {
                return true;
            }
        }
        return !ctype_print($string);
    }

    /**
     * Checks if the given string is a base64 encoded.
     * @param mixed $string
     */
    private function isBase64($string) {
        $decoded = base64_decode($string, true);
        if ($decoded === false) {
            return false;
        }

        // this solves the bug causing false positive for binary data
        if (base64_encode($decoded) !== $string) {
            return false;
        }

        return true;
    }
    
}
