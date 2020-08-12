<?php

namespace App\Http\Controllers;

class UserModelTemplateController extends Controller
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
        $content .= "class User extends Model\n";
        $content .= "{\n";
        if ($bddType !== "SQL") {
            $content .= "    protected \$collection = 'users';\n";
            $content .= "    protected \$primaryKey = 'id';\n\n";
        }
        $content .= "    /**\n";
        $content .= "     * The attributes that are mass assignable.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$fillable = [\n";
        if ($bddType == "SQL") {
            $content .= "       'name','email','password','api_token',\n";
        } else {
            $content .= "       'id','name','email','password','api_token',\n";
        }
        $content .= "    ];\n";
        $content .= "\n";
        $content .= "    /**\n";
        $content .= "     * The attributes excluded from the model's JSON form.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$hidden = [\n";
        $content .= "       'password','api_token',\n";
        $content .= "    ];\n";
        $content .= "\n";
        $content .= "    function profile_picture()\n";
        $content .= "    {\n";
        if ($bddType == "SQL") {
            $content .= "       return \$this->belongsTo('App\ProfilePicture');\n";
        } else {
            $content .= "       return \$this->embedsOne('App\ProfilePicture');\n";
        }
        $content .= "    }\n";
        $content .= "\n";
        $content .= "}\n";
        return $content;
    }
}
