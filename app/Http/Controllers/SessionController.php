<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    //
    public function create(){
        return view('sessions.create');
    }

    public function store(Request $request){
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))){
            // 登录成功
            session()->flash('success', '欢迎回来~');
            return redirect()->route('users.show', [Auth::user()]);
        } else {
            // 登录失败
            session()->flash('danger', '账号与密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(){
        Auth::logout();
        session()->flash('success', '退出成功');
        return redirect('login');
    }
}
