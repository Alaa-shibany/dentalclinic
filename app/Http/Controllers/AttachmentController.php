<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Attachment;
use App\Models\Order;
use Illuminate\Routing\Controller;

class AttachmentController extends Controller
{
    use Response;
    public function upload(Order $order){
        $att=self::lazyQueryTry(
            fn()=>Attachment::createFromRequest($order)
        );
        self::success($att);
    }
    public function delete(Order $order,Attachment $attachment){
        if($attachment->order_id!=$order->id){
            self::error(message:__('custom.wrongFlow'));
        }
        self::lazyQueryTry(
            fn()=>$attachment->delete()
        );
        self::success();
    }
}
