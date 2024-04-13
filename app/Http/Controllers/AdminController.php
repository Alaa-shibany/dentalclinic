<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Admin;
use DB;
use Illuminate\Routing\Controller;
use Str;

class AdminController extends Controller
{
    use Response;
    public function store() {
        $data=request()->validate([
            'username'=>['required','string','min:4','unique:admins,username']
        ]);
        $data['password']=Str::random(8);
        $admin=self::lazyQueryTry(
            fn()=>Admin::create($data)
        );
        $admin->generatedPassword = $data['password'];
        self::success(data:$admin);
    }
    public function index(){
        self::success(Admin::all()->toArray(),withMessage:false);
    }
    public function destroy(Admin $admin) {
        $connectedAdmin=request()->user();
        if($admin->id==$connectedAdmin->id){
            self::error(message:__('custom.deleteSelf'));
        }
        self::lazyQueryTry(
            function () use($admin){
                $admin->tokens()->delete();
                $admin->delete();
            },
            withDBTransaction:true
        );
        self::success();
    }
}

