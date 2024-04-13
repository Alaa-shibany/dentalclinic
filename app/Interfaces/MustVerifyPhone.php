<?php
namespace App\Interfaces;
interface MustVerifyPhone {
    public function hasVerifiedMobile();

    public function markMobileAsVerified();

    public function sendMobileVerificationNotification();
}

