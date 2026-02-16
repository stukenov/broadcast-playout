<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segments extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'position',
        'duration',
        'start',
        'end',
        'file_id'
    ];

    public function fileItem()
    {
        return $this->belongsTo(Files::class,'file_id');
    }
}
