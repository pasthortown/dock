<?php

namespace App\Http\Controllers;

class ProfilePictureModelTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    static function build($args) {
        $bddType = $args['bddType'];
        $content = "<?php\n";
        $content .= "\n";
        $content .= "namespace App;\n";
        $content .= "\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
        } else {
            $content .= "use Jenssegers\\Mongodb\\Eloquent\\Model;\n\n";
        }
        $content .= "\n";
        $content .= "class ProfilePicture extends Model\n";
        $content .= "{\n";
        if ($bddType !== "SQL") {
            $content .= "    protected \$collection = 'profile_pictures';\n";
            $content .= "    protected \$primaryKey = 'id';\n\n";
        }
        $content .= "    /**\n";
        $content .= "     * The attributes that are mass assignable.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$fillable = [\n";
        if ($bddType == "SQL") {
            $content .= "        'file_type','file_name','file',\n";
        } else {
            $content .= "        'id','file_type','file_name','file','id_user'\n";
        }
        $content .= "    ];\n";
        $content .= "\n";
        $content .= "    /**\n";
        $content .= "     * The attributes excluded from the model's JSON form.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$hidden = [\n";
        $content .= "       \n";
        $content .= "    ];\n";
        if ($bddType == "SQL") {
            $content .= "    \nfunction user()\n";
            $content .= "    {\n";
            $content .= "       return \$this->hasOne('App\User');\n";
            $content .= "    }\n";
        }
        $content .= "\n";
        $content .= "}\n";
        return $content;
    }
}
