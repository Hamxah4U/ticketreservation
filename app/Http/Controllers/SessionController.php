<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function create(){
        return view("users.index");
    }

    public function adminLogin(Request $request){
        $attributes = $request->validate([
            "email"=> ["required","email"],
            "password"=> "required",
        ]);

        if (!Auth::attempt([
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'status' => 'active'
        ])) {
            throw ValidationException::withMessages([
                "email" => "Invalid credentials or account not active"
            ]);
        }

        $request->session()->regenerate();

        return redirect("/dashboard");
    }

    public function destroy(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/admin/login");
    }
}
