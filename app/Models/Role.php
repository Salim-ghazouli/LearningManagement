<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{

    use HasFactory;

    /**
     * الحقول القابلة للتعبئة.
     * ملاحظة: استخدمنا 'name' ليكون متوافقاً مع الـ Migration الذي كتبناه.
     */
    protected $fillable = ['name'];

    /**
     * علاقة الدور بالمستخدمين (One-to-Many).
     * الدور الواحد (مثل instructor) يمتلك العديد من المستخدمين.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
