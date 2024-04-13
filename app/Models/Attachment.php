<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Attachment extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'file_uri',
        'file_type',
        'byAdmin',
        'order_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected static function boot(){
        parent::boot();
        self::deleted(fn($model)=>$model->deleteFile());
    }

    protected function fileUri(): Attribute{
        return Attribute::make(
            get:fn($value) =>$value==null?null:"storage/attachments/$value"
        );
    }
    public static function createFromRequest(Order $order,$byAdmin=true) {
        //add the extension to name
        $data= request()->validate([
            'attachment_file' => ['file', 'max:51200'],
            'attachment_title' => ['required_with:attachment_file','string','min:3'],
        ]);
        if(!array_key_exists('attachment_file',$data)){
            return "no action needed";
        }
        $file=request()->file('attachment_file');
        $fileName=Str::random(7)."_".now()->format('Y-m-d_H-i-s');
        $fileName.=".{$file->extension()}";
        $file->storeAs("public/attachments/",$fileName);
        $fileType=self::getFileType($file->extension());
        $att=Attachment::create([
            'file_uri'=>$fileName,
            'file_type'=>$fileType,
            'title'=>request('attachment_title'),
            'byAdmin'=>$byAdmin,
            'order_id'=>$order->id
        ]);
        return $att;
    }

    public function deleteFile(){
        $oldName=$this->getRawOriginal('file_uri');
        if($oldName!=null){
            array_map('unlink'
            ,glob("../storage/app/public/attachments/$oldName"));
        }
    }

    private static function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'mkv', 'flv'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } else {
            return 'other';
        }
    }
}
