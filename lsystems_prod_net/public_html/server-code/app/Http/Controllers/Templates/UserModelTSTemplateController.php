<?php

namespace App\Http\Controllers;

class UserModelTSTemplateController extends Controller
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
        $content = "import { ProfilePicture } from './ProfilePicture';\n";
        $content .= "\n";
        $content .= "export class User {\n";
        $content .= "   id: number;\n";
        $content .= "   name: String;\n";
        $content .= "   email: String;\n";
        $content .= "   password: String;\n";
        $content .= "   api_token: String;\n";
        $content .= "   profile_picture: ProfilePicture;\n";
        $content .= "\n";
        $content .= "   constructor() {\n";
        $content .= "      this.id = 0;\n";
        $content .= "      this.name = '';\n";
        $content .= "      this.email = '';\n";
        $content .= "      this.password = '';\n";
        $content .= "      this.api_token = '';\n";
        $content .= "      this.profile_picture = new ProfilePicture();\n";
        $content .= "   }\n";
        $content .= "}\n";
        return $content;
    }
}
