<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Login',
        ];
        return view('contents.auth.login', $data);
    }
    public function authenticate()
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()->with('messageFailed', 'Opps, Login gagal');
        }

        $username = htmlspecialchars($_POST['username'], true);

        if (User::where('username', $username)->exists()) {
            $user = User::where('username', $username)->first();

            if (Auth::attempt([
                'email' => $user->email,
                'password' => $_POST['password']
            ])) {

                request()->session()->regenerate();

                return redirect()->route('dasbor');
            }

            return back()->with('messageFailed', 'Opps, Login gagal');
        }
        return back()->with('messageFailed', 'Opps, Login gagal');
    }
}
