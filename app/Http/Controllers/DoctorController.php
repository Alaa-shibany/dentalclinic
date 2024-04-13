<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use Illuminate\Routing\Controller;

class DoctorController extends Controller
{
    use Response;
    public function viewProfile() {
        $doctor = self::getConnectedDoctor();
        $doctor->ordersCount=$doctor->orders()->count();
        self::success($doctor);
    }
    public function editProfile() {
        $doctor = self::getConnectedDoctor();
        $data = request()->validate([
            'username'=>['string','min:3'],
            'password'=>['string','between:7,15'],
            'location'=>['string','min:4'],
            'image'=>'file|mimes:jpeg,png,jpg|max:20048'
        ]);
        self::lazyQueryTry(
            function()use($doctor,$data){
                $doctor->update($data);
                $doctor->updateProfilePicture();
            }
        );
        self::success($doctor);
    }
}
