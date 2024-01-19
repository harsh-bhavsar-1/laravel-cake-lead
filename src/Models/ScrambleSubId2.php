<?php

namespace LaravelCake\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrambleSubId2 extends Model
{
    use HasFactory;

    protected $table = 'scramble_subids2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sub_id2',
        'scramble_sub_id2',
    ];

}
