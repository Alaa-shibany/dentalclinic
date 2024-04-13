<?php
namespace App\Traits;

use Log;

Trait MustVerifyPhone {
    public function hasVerifiedMobile(){
        return ! is_null($this->phone_verified_at);
    }

    public function markMobileAsVerified(){
        return $this->forceFill([
            'phone_verified_at'=>$this->freshTimeStamp(),
        ])->save();
    }

    public function sendMobileVerificationNotification(){
        $length = 7; // Length of the random string
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= random_int(0, 9); // Append a random digit (0-9)
        }
        //You have to choose a sms provided and configure the connection with it and then
        //send the generated code via it, for now the code will be from 1 to 7

        //TODO: remove this when you completed the above
        $randomNumber="1234567";
        //--------------

        $this->verification_code= $randomNumber;
        $this->save();
        Log::info("Verification Code is:".$this->verification_code);
    }
}

