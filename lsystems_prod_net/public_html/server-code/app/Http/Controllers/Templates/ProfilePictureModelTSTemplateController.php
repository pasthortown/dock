<?php

namespace App\Http\Controllers;

class ProfilePictureModelTSTemplateController extends Controller
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
        $content = "export class ProfilePicture {\n";
        $content .= "    id: number;\n";
        $content .= "    file_type: String;\n";
        $content .= "    file_name: String;\n";
        $content .= "    file: String;\n";
        $content .= "\n";
        $content .= "    constructor() {\n";
        $content .= "       this.id  = 0;\n";
        $content .= "       this.file_type = '';\n";
        $content .= "       this.file = '';\n";
        $content .= "       this.file_name = '';\n";
        $content .= "    }\n";
        $content .= " }\n";
        $content .= "\n";
        return $content;
    }
}
