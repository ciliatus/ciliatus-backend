<?php

namespace App\Ciliatus\Api\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function check__show()
    {
        return Auth::user() ? $this->respondWithData(Auth::user()->transform()) : $this->respondUnauthenticated();
    }

}
