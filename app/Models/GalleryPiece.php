<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Str;

class GalleryPiece extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_uri',
        'description',
        'address',
        'favorite'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected function imageUri(): Attribute{
        return Attribute::make(
            get:fn($value) =>$value==null?null:"storage/gallery/$value"
        );
    }

    public function updateCoverPhoto(Request $request) {
        // get all the files with the same name..then delete them
        //glob gets all the fiels that their path matches the patter provided
        $this->deleteCoverPhoto();
        //add the extension to name
        $image=$request->file('image');
        $imageName=Str::random(7)."_".now()->format('Y-m-d_H-i-s');
        $imageName.=".{$image->extension()}";
        $image->storeAs("public/gallery/",$imageName);
        $this->image_uri=$imageName;
    }
    public function deleteCoverPhoto(){
        $oldName=$this->getRawOriginal('image_uri');
        if($oldName!=null){
            array_map('unlink'
            ,glob("../storage/app/public/gallery/$oldName"));
        }
    }
}
