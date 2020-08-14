<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Mobile extends Model
{
    protected $collection = 'mobiles';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','name','description','number','id_user',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    function mobile_type()
    {
       return $this->embedsOne('App\MobileType');
    }

    function mobile_attachment()
    {
       return $this->embedsMany('App\MobileAttachment');
    }

}