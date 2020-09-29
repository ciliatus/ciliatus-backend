<?php

namespace App\Ciliatus\Api\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function check__show()
    {
        $user = auth()->user();
        $r = $this->request->user();
        return Auth::user() ? $this->respondWithData(Auth::user()->transform()) : $this->respondUnauthenticated();
    }

}
