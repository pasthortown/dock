<?php

namespace App\Http\Controllers;

class EnvironmentsTemplateController extends Controller
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

    static function buildEnvironmentsProd($args) {
        $moduleName = $args['moduleName'];
        $content = "export const environment = {\n";
        $content .= "   production: true,\n";
        $content .= "   api_".strtolower($moduleName).": 'http://localhost:8000/',\n";
        $content .= "   gmapapiKey: 'AIzaSyCZQgG8L6ntkJZarveWX9mvy9f9MMOoNDA',\n";
        $content .= "};";
        return ["Content"=>$content];
    }

    static function buildEnvironments($args) {
        $moduleName = $args['moduleName'];
        $content = "export const environment = {\n";
        $content .="   production: false,\n";
        $content .="   api_".strtolower($moduleName).": 'http://localhost:8000/',\n";
        $content .="   gmapapiKey: 'AIzaSyCZQgG8L6ntkJZarveWX9mvy9f9MMOoNDA',\n";
        $content .="};";
        return ["Content"=>$content];
    }
}
