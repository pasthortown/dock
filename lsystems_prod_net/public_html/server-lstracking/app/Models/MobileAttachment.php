<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class MobileAttachment extends Model
{
    protected $collection = 'mobile_attachments';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','mobile_attachment_file_type','mobile_attachment_file_name','mobile_attachment_file',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

}