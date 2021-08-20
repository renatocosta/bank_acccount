<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TestController extends Controller
{

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"USER"},
     *     summary="GET LIST OF USERS",
     *     operationId="users",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index()
    {
dd(333);
    }
}