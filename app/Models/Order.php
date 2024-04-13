<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'center_name',
        'patient_name',
        'veneer',
        'crown',
        'inlay_onlay',
        'ceramicBuildUp',
        'ceramicFacing',
        'fullAnatomic',
        'fullMetal',
        'PFM',
        'DSD',
        'mockUp',
        'printedModel',
        'PMMA',
        'teethCount',
        'connected',
        'notes',
        'doctor_id',
        'upper_color',
        'middle_color',
        'lower_color',
        'submitted_at',
        'status',
        'stage',
        'separate'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    /**
     * Get all of the teeth for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teeth(){
        return $this->hasMany(Tooth::class);
    }
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
    public function attachments(){
        return $this->hasMany(Attachment::class);
    }
    public function doctorAttachments(){
        return $this->hasMany(Attachment::class)
        ->where('byAdmin',false);
    }
    public function adminAttachments(){
        return $this->hasMany(Attachment::class)
        ->where('byAdmin',true);
    }

}
