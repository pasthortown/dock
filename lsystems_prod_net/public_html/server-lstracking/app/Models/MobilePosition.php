<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class MobilePosition extends Model
{
    protected $collection = 'mobile_positions';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','ubication','id_mobile',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}