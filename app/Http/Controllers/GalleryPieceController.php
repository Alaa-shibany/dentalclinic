<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Models\GalleryPiece;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class GalleryPieceController extends Controller
{
    use Response;
    public function index() {
        $others = GalleryPiece::orderByDesc('created_at')->get();
        $favorite = $others->reject(fn($p)=>!$p->favorite)->values();
        self::success([
            'favorite'=>$favorite,
            'others'=>$others
        ]);
    }

    public function store(){
        $data=request()->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg|max:5048',
            'description' => 'required|string',
            'address' => 'required|string',
            'favorite' => 'boolean',
        ]);
        $piece=self::lazyQueryTry(
            function()use ($data){
                $data['image_uri']="temp";
                $piece=GalleryPiece::create($data);
                $piece->updateCoverPhoto(request());
                $piece->save();
                return $piece;
            },
            withDBTransaction:true,
            rollOnAbort:true
        );
        self::success($piece);
    }
    public function update(GalleryPiece $galleryPiece){
        $data=request()->validate([
            'image' => 'file|mimes:jpeg,png,jpg|max:5048',
            'description' => 'string',
            'address' => 'string',
            'favorite' => 'boolean',
        ]);
        $piece=self::lazyQueryTry(
            function()use ($galleryPiece,$data){
                $galleryPiece->update($data);
                if(array_key_exists('image',$data)){
                    $galleryPiece->updateCoverPhoto(request());
                    $galleryPiece->save();
                }
                return $galleryPiece;
            },
            withDBTransaction:true,
            rollOnAbort:true
        );
        self::success();
    }
    public function destroy(GalleryPiece $galleryPiece){
        self::lazyQueryTry(
            fn()=>$galleryPiece->delete()
        );
        try {
            $galleryPiece->deleteCoverPhoto();
        }catch(Exception $e){
            self::reportError($e);
        }
        self::success();
    }
}
