<?php

namespace App\Http\Controllers;

class RoutingModuleTemplateController extends Controller
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
        $content = "import { NgModule } from '@angular/core';\n";
        $content .= "import { RouterModule, Routes } from '@angular/router';\n";
        $content .= "import { ".$tableNameSingular."Component } from './".strtolower($tableNameSingular).".component';\n\n";
        $content .= "const routes: Routes = [\n";
        $content .= "   {\n";
        $content .= "      path: '',\n";
        $content .= "      component: ".$tableNameSingular."Component\n";
        $content .= "   }\n";
        $content .= "];\n\n";
        $content .= "@NgModule({\n";
        $content .= "   imports: [RouterModule.forChild(routes)],\n";
        $content .= "   exports: [RouterModule]\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."RoutingModule {}\n";
        return $content;
    }
}
