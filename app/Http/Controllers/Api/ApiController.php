<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $data;
    protected $message;
    protected $errors;
    protected $status;

    public function __construct()
    {
        $this->data = [];
        $this->errors = [];
        $this->message = 'OK';
        $this->data = [];
        $this->status = 200;
    }

    public function respond()
    {
        return response()->json([
                'message' => $this->message,
                'errors' => $this->errors,
                'data' => $this->data
            ], $this->status);
    }

    public function respondNotFound()
    {
        $this->status = 404;
        $this->message = 'Not Found';

        return $this->respond();
    }

    public function respondUnauthorized()
    {
        $this->status = 401;
        $this->message = 'Unauthorized';

        return $this->respond();
    }

    public function respondForbidden()
    {
        $this->status = 403;
        $this->message = 'Forbidden';

        return $this->respond();
    }

    public function respondOk()
    {
        $this->status = 200;
        $this->message = 'OK';

        return $this->respond();
    }

    public function respondOkWithData($data)
    {
        $this->data = $data;

        return $this->respondOk();
    }
}
