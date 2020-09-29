<?php

namespace App\Ciliatus\Api\Http\Controllers;

use App\Ciliatus\Api\Attributes\CustomAction;
use App\Ciliatus\Api\Http\Controllers\Actions\Action;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    #[CustomAction(Action::SHOW)]
    public function check__show()
    {
        return Auth::user() ? $this->respondWithData(Auth::user()->transform()) : $this->respondUnauthenticated();
    }

}
