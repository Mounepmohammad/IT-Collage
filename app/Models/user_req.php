<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_req extends Model
{
    use HasFactory;

   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'student_name',
        'collage_number',
        'photo1',
        'photo2',
        'photo3',
        'photo4',
        'photo5',
        'state',
        'descreption',
        'type',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
