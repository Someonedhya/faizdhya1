<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LoginRegisterController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.akun.index', compact('users'));
    }
    
    public function create()
    {
        return view('admin.akun.create');
    }

    public function edit()
    {
        return view('admin.akun.edit');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            'usertype' => 'required'
        ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'usertype' => 'admin'
    ]);

    return redirect()->route('akun.index')->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'

        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            if($request->user()->usertype == 'admin'){
                return redirect('admin/dashboard')->withSuccess('You have successfully logged in');
            }
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyinput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withSuccess('You have logged out successfully');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'usertype' => 'required'
        ]);

        //redirect to index
        return redirect()->route('akun.edit' , $id)->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function updateEmail(Request $request, $id):RedirectResponse
    {
        //validate form
        $validated = $request->validate([
            'email' => 'required|email|max:250|unique:users'
        ]);

        //get post by id
        $datas = User::findOrFail($id);
        //edit akun

        $datas->update([
            'email' => $request->email

        ]);
        //redirect to index
        return redirect()->route('akun.edit' , $id)->with(['success' => 'Email Berhasil Disimpan']);
    }

    public function updatePassword(Request $request, $id): RedirectResponse
    {
        //validate form
        $validated = $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        //get post by ID
        $datas = User::finOrFail($id);
        //edit akun

        $datas->update([
            'password' =>Hash::make($request->password)
        ]);
        //redirect to index
        return redirect()->route('akun.edit' , $id)->with(['success' => 'Password Berhasil Disimpan']);
    }
    
}

