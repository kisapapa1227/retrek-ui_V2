<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function login(Request $request)
    {
        if (empty($request->email)){
            return view('login');
            exit;
        }

        if (!empty($request->email)){

            $param =['email'=>$request->email];
            $user =DB::select("select * from t_lar_admin_user where email =:email",$param);

        if (count($user) === 0){
                return view('login', ['login_error' => '1']);
            }
        
            if (count($user)){

                if (Hash::check($request->password, $user[0]->password)) {
                    //Hash::checkパス一致した場合;
                    session(['name'  => $user[0]->name]);
                    session(['email' => $user[0]->email]);
                    return redirect(url('/lar-admin/index'));
                }
                else{
                    return view('login', ['login_error' => '1']);
                }
            }
        }//if (!empty($request->email))
            return view('login');

    } 

    public function indexView(Request $request)
    {
        $username = session('name');//セッションから名前取得
        return view('index')->with('username',$username);
    }

    public function logout()
    {
        session(['name'  => null]);
        session(['email' => null]);
    }
    public function logoutView()
    {
        self::logout();
        return view('logout');
    }

}
?>
