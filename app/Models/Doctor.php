<?php

namespace App\Models;

use App\Interfaces\MustVerifyPhone;
use App\Traits\MustVerifyPhone as TraitsMustVerifyPhone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Str;

class Doctor extends User implements MustVerifyPhone
{
    use HasApiTokens, HasFactory, Notifiable, TraitsMustVerifyPhone;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username','password','profile_picture','phone','location'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'phone_verified_at',
        'verification_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    protected function profilePicture(): Attribute{
        return Attribute::make(
            get:fn($value) =>$value==null?null:"storage/profilePictures/$value"
        );
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function updateProfilePicture() {
        $this->deleteProfilePicture();
        //add the extension to name
        if(!request()->hasFile('image')){
            return "nothing to do.";
        }
        $image=request()->file('image');
        $imageName=Str::random(7)."_".now()->format('Y-m-d_H-i-s');
        $imageName.=".{$image->extension()}";
        $image->storeAs("public/profilePictures/",$imageName);
        $this->profile_picture=$imageName;
        $this->save();
    }
    public function deleteProfilePicture(){
        $oldName=$this->getRawOriginal('profile_picture');
        if($oldName!=null){
            array_map('unlink'
            ,glob("../storage/app/public/profilePictures/$oldName"));
        }
    }
}
