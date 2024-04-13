<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Doctor;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    use Response;
    public function adminLogin(){
        $data=request()->validate([
            'username'=>['required','string','min:3'],
            'password'=>['required','string','min:1']
        ]);
        if(auth()->guard('admin')->attempt($data)){
            $admin=request()->user('admin');
            if($admin->tokens()->count()>30){
                abort(400,__('auth.tooManyTokens'));
            }
            $type=$admin->super?"superAdmin":"admin";
            $token=request()->user('admin')
            ->createToken(request()->ip(),[$type])
            ->plainTextToken;
            $data=[
                'token'=>$token,
                'type'=>$type,
                'admin'=>$admin
            ];
            self::success(data:$data,message:__('auth.success'));
        }else {
            self::error(message:__('auth.failed'));
        }

    }

    public function doctorLogin(){
        $data=request()->validate([
            'phone' => ['required', 'string', 'between:7,16','regex:/^\+\d+$/'],
            'password'=>['required','string','between:7,40']
        ]);
        if(auth()->attempt($data)){
            $doctor=request()->user();
            if($doctor->tokens()->count()>15){
                abort(400,__('auth.tooManyTokens'));
            }
            $type='doctor';
            $token=request()->user()
            ->createToken(request()->ip(),[$type])
            ->plainTextToken;
            $data=[
                'token'=>$token,
                'type'=>$type,
                'doctor'=>$doctor
            ];
            self::success(data:$data,message:__('auth.success'));
            // if($doctor->hasVerifiedMobile()){
            //     self::success(data:$data,message:__('auth.success'));
            // }
            // $doctor->sendMobileVerificationNotification();
            // self::success(data:$data,message:__('auth.login.success.mustVerify'),code:201);

        }else {
            self::error(message:__('auth.failed'));
        }

    }

    public function logout(){
        request()->user()->currentAccessToken()->delete();
        self::success();
    }
    public function signup(){
        $data =request()->validate([
            'username' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|unique:doctors,phone|between:7,15|regex:/^\+\d+$/',
            'password' => 'required|string|between:7,40',
            'image'=>'file|mimes:jpeg,png,jpg|max:20048'
        ]);
        $result=self::lazyQueryTry(
            function()use($data){
                $doctor=Doctor::create($data);
                $doctor->updateProfilePicture();
                $type='doctor';
                $doctor->sendMobileVerificationNotification();
                $token=$doctor
                ->createToken(request()->ip(),[$type])
                ->plainTextToken;
                return [
                    'doctor'=>$doctor,
                    'token'=>$token,
                    'type'=>$type
                ];
            }
        );
        self::success($result,message:__('auth.accountCreated'));
    }

    public function verifyCode(){
        $doctor=self::getConnectedDoctor();
        if($doctor->hasVerifiedMobile()){
            self::success(message:__('custom.already_verified'));
        }
        $code=request()->validate([
            'code'=>'required|string|regex:/^\d{7}$/',
        ])['code'];
        self::lazyQueryTry(
            function()use($doctor,$code){
                if($doctor->verification_code==$code){
                    $doctor->markMobileAsVerified();
                }else{
                    self::error(message:__('custom.verify_code_wrong'));
                }
            }
        );
        self::success();
    }

    private function formatPhoneNumber($number){
        $number=str_replace(' ','',$number);
        $number=str_replace('+','00',$number);
        return $number;
    }
}
