<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    function  __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail'],
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    function confirmEmail($token){
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();


        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功!');
        return redirect()->route('users.show', [$user]);
    }

    public function index(){
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    //
    public function create(){
        return view('users.confirmEmail');
    }

    public function show(User $user){
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('users.show', compact('user', 'statuses'));
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|max:50',
            'email'=> 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮箱已发送到您的邮箱，请注意查收。');
        return redirect('/');
//        Auth::login($user);
//        session()->flash('success', '欢迎，您将开启一段laravel之旅');
//        return redirect()->route('users.show', $user);
    }

    public function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用，请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject){
            $message->to($to)->subject($subject);
        });
    }

    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request){
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'required|confirmation|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料修改成功');

        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user){
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户');
        return back();
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
