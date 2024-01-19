<?php

namespace LaravelCake\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'page',
        'query_string',
        'ckm_offer_id',
        'oc',
        'reqid',
        's1',
        's2',
        's3',
        'subid',
        'referrer',
        'submitted',
        'tax_debt',
        'neustar',
        'neustar_disposition',
    ];
}
