<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class interview extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'group_id',
        'doctor_id',
        'from',
        'to',
        'date',
        'goal',
        'state',
        'note',
        'reason',
        'title',

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(doctor::class);
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(group::class);
    }

}
