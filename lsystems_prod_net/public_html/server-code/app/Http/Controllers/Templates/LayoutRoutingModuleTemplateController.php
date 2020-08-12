<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class LayoutRoutingModuleTemplateController extends Controller
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
        $models = $args['models'];
        $moduleName = $args['moduleName'];
        $content = "import { NgModule } from '@angular/core';\n";
        $content .= "import { RouterModule, Routes } from '@angular/router';\n";
        $content .= "import { LayoutComponent } from './layout.component';\n\n";
        $content .= "const routes: Routes = [\n";
        $content .= "   {\n";
        $content .= "      path: '',\n";
        $content .= "      component: LayoutComponent,\n";
        $content .= "      children: [\n";
        $content .= "         {\n";
        $content .= "            path: '',\n";
        $content .= "            redirectTo: 'main'\n";
        $content .= "         },\n";
        $content .= "         {\n";
        $content .= "            path: 'main',\n";
        $content .= "            loadChildren: './main/main.module#MainModule'\n";
        $content .= "         },\n";
        $content .= "         {\n";
        $content .= "            path: 'profile',\n";
        $content .= "            loadChildren: './profile/profile.module#ProfileModule'\n";
        $content .= "         },\n\n";
        $content .= "         //".$moduleName."\n\n";
        foreach($models as $modelo) {
            $model = $modelo['Table']['nameSingular'];
            $content .= "         {\n";
            $content .= "            path: '".UtilitiesController::checkNames($model)."',\n";
            $content .= "            loadChildren: './CRUD/".strtoupper($moduleName)."/".$model."/".strtolower($model).".module#".$model."Module'\n";
            $content .= "         },\n";
        }
        $content .= "         {\n";
        $content .= "            path: 'blank',\n";
        $content .= "            loadChildren: './blank-page/blank-page.module#BlankPageModule'\n";
        $content .= "         },\n";
        $content .= "         {\n";
        $content .= "            path: 'not-found',\n";
        $content .= "            loadChildren: './not-found/not-found.module#NotFoundModule'\n";
        $content .= "         },\n";
        $content .= "         {\n";
        $content .= "            path: '**',\n";
        $content .= "            redirectTo: 'not-found'\n";
        $content .= "         }\n";
        $content .= "      ]\n";
        $content .= "   }\n";
        $content .= "];\n\n";
        $content .= "@NgModule({\n";
        $content .= "   imports: [RouterModule.forChild(routes)],\n";
        $content .= "   exports: [RouterModule]\n";
        $content .= "})\n";
        $content .= "export class LayoutRoutingModule {}";
        return ["Content"=>$content];
    }
}
