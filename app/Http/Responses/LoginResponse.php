<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return redirect()->intended(
            auth()->user()->is_admin ? route('admin.dashboard') : route('grader.dashboard')
        );
    }
}
