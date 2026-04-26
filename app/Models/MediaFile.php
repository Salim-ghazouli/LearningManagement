<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model {
    protected $fillable = ['file_name', 'file_path', 'mediable_id', 'mediable_type', 'collection', 'uploaded_by', 'size'];

    public function mediable() { return $this->morphTo(); }
}
