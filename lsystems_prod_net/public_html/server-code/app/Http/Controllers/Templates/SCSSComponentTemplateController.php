<?php

namespace App\Http\Controllers;

class SCSSComponentTemplateController extends Controller
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

    static function build($args, $type) {
        $tableColumns = $args['Columns'];
        $content = " ";
        $tieneBoolean = false;
        foreach ($tableColumns as $column) {
            if ($column['type']==='boolean') {
                $tieneBoolean = true;
            }
        }
        if($tieneBoolean && $type === 'bootstrap') {
            $content = ".switch {\n";
            $content .= "   position: relative;\n";
            $content .= "   display: inline-block;\n";
            $content .= "   width: 60px;\n";
            $content .= "   height: 34px;\n";
            $content .= "}\n\n";
            $content .= ".switch input {\n";
            $content .= "   display: none;\n";
            $content .= "}\n\n";
            $content .= ".slider {\n";
            $content .= "   position: absolute;\n";
            $content .= "   cursor: pointer;\n";
            $content .= "   top: 0;\n";
            $content .= "   left: 0;\n";
            $content .= "   right: 0;\n";
            $content .= "   bottom: 0;\n";
            $content .= "   background-color: #ccc;\n";
            $content .= "   -webkit-transition: 0.4s;\n";
            $content .= "   transition: 0.4s;\n";
            $content .= "}\n\n";
            $content .= ".slider:before {\n";
            $content .= "   position: absolute;\n";
            $content .= "   content: \"\";\n";
            $content .= "   height: 26px;\n";
            $content .= "   width: 26px;\n";
            $content .= "   left: 4px;\n";
            $content .= "   bottom: 4px;\n";
            $content .= "   background-color: white;\n";
            $content .= "   -webkit-transition: 0.4s;\n";
            $content .= "   transition: 0.4s;\n";
            $content .= "}\n\n";
            $content .= "input:checked + .slider {\n";
            $content .= "   background-color: #218838;\n";
            $content .= "}\n\n";
            $content .= "input:focus + .slider {\n";
            $content .= "   box-shadow: 0 0 1px #218838;\n";
            $content .= "}\n\n";
            $content .= "input:checked + .slider:before {\n";
            $content .= "   -webkit-transform: translateX(26px);\n";
            $content .= "   -ms-transform: translateX(26px);\n";
            $content .= "   transform: translateX(26px);\n";
            $content .= "}\n\n";
            $content .= ".slider.round {\n";
            $content .= "   border-radius: 34px;\n";
            $content .= "}\n\n";
            $content .= ".slider.round:before {\n";
            $content .= "   border-radius: 50%;\n";
            $content .= "}\n\n";
        }
        return $content;
    }
}
