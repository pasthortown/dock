<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class MobileType extends Model
{
    protected $collection = 'mobile_types';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}