<?php

namespace App\Http\Controllers;

class ModuleSpecTemplateController extends Controller
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
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { ".$tableNameSingular."Module } from './".strtolower($tableNameSingular).".module';\n\n";
        $content .= "describe('".$tableNameSingular."Module', () => {\n";
        $content .= "   let blackPageModule: ".$tableNameSingular."Module;\n\n";
        $content .= "   beforeEach(() => {\n";
        $content .= "      blackPageModule = new ".$tableNameSingular."Module();";
        $content .= "   });\n\n";
        $content .= "   it('should create an instance', () => {\n";
        $content .= "      expect(blackPageModule).toBeTruthy();\n";
        $content .= "   });\n";
        $content .= "});";
        return $content;
    }
}
