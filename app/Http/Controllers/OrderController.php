<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\Attachment;
use App\Models\Doctor;
use App\Models\Order;
use App\Models\Tooth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;

class OrderController extends Controller
{
    use Response;
    public function index(){
        $orders = Order::orderByDesc('submitted_at')->get();
        $resultOrders=$orders->map(
            fn($order)=>[
                'id'=>$order->id,
                'title'=>"Ctr.".$order->center_name."/Dr.".$order->doctor->username."/Pt.".$order->patient_name,
                'status'=>$order->status,
                'submitted_at'=>$order->submitted_at,
                'patient_name'=>$order->patient_name,
                'center_name'=>$order->center_name,
                'doctor_name'=>$order->doctor->username,
                'doctor_id'=>$order->doctor->id
            ]
        );
        self::success($resultOrders);
    }

    public function store(){
        $doctor=self::getConnectedDoctor();
        // App::setLocale('ar');
        request()->validate([
            'patient_name'=>['required','string','min:3'],
            'center_name'=>['required','string','min:3'],
            'teeth'=>['required','array','min:1'],
            'teeth.*'=>['numeric','between:1,32'],
        ]);
        $data=collect(request()->all())->filter()->toArray();
        $order=self::lazyQueryTry(
            function()use($doctor,$data){
                $data['doctor_id']=$doctor->id;
                $data['submitted_at']=now();
                $order=Order::create($data);
                $teethData=collect($data['teeth'])->map(
                    fn($t)=>[
                        'created_at'=>now(),
                        'updated_at'=>now(),
                        'toothId'=>$t,
                        'order_id'=>$order->id
                    ]
                );
                Tooth::insert($teethData->toArray());
                Attachment::createFromRequest($order,false);
                return $order;
            },
            withDBTransaction:true,
            rollOnAbort:true,
        );
        $rOrder=Order::with(['adminAttachments','doctorAttachments','teeth'])
        ->find($order->id);
        self::success($rOrder);
    }
    public function update(Order $order){
        $data=request()->all();
        self::lazyQueryTry(
            function()use($order,$data){
                $order->update($data);
                if(array_key_exists('teeth',$data) && !empty($data['teeth'])){
                    $order->teeth()->delete();
                    $teethData=collect($data['teeth'])->map(
                        fn($t)=>[
                            'created_at'=>now(),
                            'updated_at'=>now(),
                            'toothId'=>$t,
                            'order_id'=>$order->id
                        ]
                    );
                    Tooth::insert($teethData->toArray());
                }
                return $order;
            },
            withDBTransaction:true,
        );
        $order->load(['adminAttachments','doctorAttachments','teeth']);
        self::success($order);
    }

    public function show(Order $order){
        $user=request()->user();
        if($user instanceof Doctor && $order->doctor->id!=$user->id){
            self::error(403,__('custom.forbidden'));
        }
        $order->load([
            'doctor',
            'teeth',
            'doctorAttachments',
            'adminAttachments'
        ]);
        self::success($order);
    }

    public function showDoctorsOrders() {
        $doctor=self::getConnectedDoctor();
        $orders=$doctor->orders()->orderByDesc('submitted_at')->get();
        $result=$orders->map(
            fn($order)=>[
                'id'=>$order->id,
                'title'=>$order->center_name."/".$order->patient_name,
                'status'=>$order->status,
                'stage'=>$order->stage,
                'submitted_at'=>$order->submitted_at,
                'patient_name'=>$order->patient_name,
                'center_name'=>$order->center_name,
            ]
        );
        self::success($result);
    }
    public function makeStatusAccepted(Order $order){
        $user=request()->user();
        if($order->status=="Denied" && !$user->tokenCan('superAdmin')){
            self::error(400,__('custom.status.deniedOrder_onlySuper'));
        }
        if($order->status=='Done'){
            self::error(400,__('custom.status.pending'));
        }
        $order->status='Accepted';
        self::lazyQueryTry(
            fn()=>$order->save()
        );
        self::success($order);
    }
    public function makeStatusDenied(Order $order){
        $user=request()->user();
        if($order->status=="Accepted" && !$user->tokenCan('superAdmin')){
            self::error(400,__('custom.status.acceptedOrder_onlySuper'));
        }
        if($order->status=='Done'){
            self::error(400,__('custom.status.pending'));
        }
        $order->status='Denied';
        self::lazyQueryTry(
            fn()=>$order->save()
        );
        self::success($order);
    }
    public function makeStatusDone(Order $order){
        if($order->status!='Accepted'){
            self::error(400,__('custom.status.mustBeAccepted'));
        }
        $order->status='Done';
        self::lazyQueryTry(
            fn()=>$order->save()
        );
        self::success($order);
    }

    public function destroy(Order $order){
        self::lazyQueryTry(
            function()use($order){
                foreach($order->attachments as $att){
                    $att->deleteFile();
                }
                $order->delete();
            }
        );
        self::success();
    }
}
