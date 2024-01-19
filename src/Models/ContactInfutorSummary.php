<?php

namespace LaravelCake\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInfutorSummary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contacts_infutor_summury';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'email_address',
        'primary_phone',
        'summary',
    ];

}
