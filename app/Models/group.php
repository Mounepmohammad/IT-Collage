<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'student1',
        'student2',
        'student3',
        'student4',
        'student5',
        'student6',
        'type',

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
