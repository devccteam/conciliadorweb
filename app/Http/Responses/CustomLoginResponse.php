<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as FilamentLoginResponse;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements FilamentLoginResponse
{
    
    public function toResponse($request)
    {

        $currentUser = Auth::user();

        if ($currentUser->is_admin) {
            return redirect('/admin');
        }


        if ($currentUser->contracts->count() > 1) {
            return redirect('/gateway/select-contract');
        }

        return redirect('/'); 
    }
}