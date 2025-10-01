<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewUserRegistration;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'is_approved' => false,
        ]);

        event(new Registered($user));

        $adminEmail = config('app.admin_email', env('ADMIN_EMAIL'));
        // @TODO descomentar quando o email estiver configurado
         Mail::to($adminEmail)->send(new NewUserRegistration($user));

        return redirect()->route('login')
            ->with('status', 'Registro realizado com sucesso! Aguarde a aprovação do administrador.');
    }
}
