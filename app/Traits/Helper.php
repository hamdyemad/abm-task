<?php

namespace App\Traits;

trait Helper {

    public function send_response($status = true, $data = [], $errors = [], $message = '', $code = 200) {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $code);
    }
}
