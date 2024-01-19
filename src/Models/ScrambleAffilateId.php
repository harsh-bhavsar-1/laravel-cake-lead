<?php

namespace LaravelCake\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrambleAffilateId extends Model
{
    use HasFactory;

    protected $table = 'scramble_affilateids';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sub_id',
        'scramble_affilate_id',
    ];

}
