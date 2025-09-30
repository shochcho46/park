<?php

namespace App;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse($data = null, string|null $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data instanceof JsonResponse) {
            $responseData = $data->getData();

            if (isset($responseData->data)) {
                $response['data'] = (array) $responseData->data;

            }
            if (isset($responseData->meta)) {
                $response = array_merge($response, (array) $responseData->meta);
            }
            return response()->json($response, $data->status());
        }

        if ($data) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);

    }


    protected function errorResponse($message = null, int $code = 400, array|null $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
