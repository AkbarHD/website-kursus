<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function auth()
    {
        return view('auth');
    }

    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ],
            [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.unique' => 'Email is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]
        );

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // smtp
            Notification::send($user, new VerifyEmail());

            return redirect()->route('auth')->with('success', 'Register success! silahkan cek email untuk verifikasi');
        } catch (\Throwable $e) {
            return redirect()->route('auth')->with('error', 'Register failed!' . $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]
        );

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->email_verified_at) {
                $request->session()->regenerate();
                if (Auth::user()->role == 'admin') {
                    return redirect()->route('admin')->with('success', 'Selamat datang admin!');
                } else {
                    return redirect()->route('user')->with('success', 'Anda berhasil masuk!');
                }
            } else {
                Auth::logout();
                return back()->with('error', 'Email belum terverifikasi');
            }
        }

        return back()->with('error', 'Login failed! Email or password is incorrect');
    }

    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        if (sha1($user->getEmailForVerification()) !== $hash) {
            return redirect()->route('auth')->with('error', 'Link verifikasi tidak valid');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('auth')->with('success', 'Email and sudah terverifikasi');
        }

        if ($user->markEmailAsVerified()) {
            return redirect()->route('auth')->with('success', 'Email anda berhasil diverifikasi');
        }

        return redirect()->route('auth')->with('error', 'Gagal verifikasi email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Anda berhasil Logout!');
    }
}
