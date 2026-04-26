<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $message = null, $status = 200)
    {
        $array = [
            'data'    => $data,
            'message' => $message,
            'status'  => in_array($status, [200, 201, 202]) ? true : false,
        ];

        return response()->json($array, $status);
    }
    public function validationError($validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'data' => null,
            'message' => 'Validation failed',
            'status' => false,
            'errors' => $errors
        ], 422);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }
}
