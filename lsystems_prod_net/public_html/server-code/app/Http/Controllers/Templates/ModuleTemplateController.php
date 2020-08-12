<?php

namespace App\Http\Controllers;

class ModuleTemplateController extends Controller
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

    static function build($args, $type, $moduleName, $bddType) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $esAdjunto = $args['esAdjunto'];
        $relationships = $args['RelationShip'];
        $tableColumns = $args['Columns'];
        $content = "import { CommonModule } from '@angular/common';\n";
        $content .= "import { NgModule } from '@angular/core';\n";
        $content .= "import { FormsModule } from '@angular/forms';\n";
        if($type === 'bootstrap'){
            $content .= "import { NgbModal } from '@ng-bootstrap/ng-bootstrap';\n";
        }
        $content .= "import { ".$tableNameSingular."RoutingModule } from './".strtolower($tableNameSingular)."-routing.module';\n";
        $content .= "import { ".$tableNameSingular."Component } from './".strtolower($tableNameSingular).".component';\n";
        $content .= "import { ".$tableNameSingular."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($tableNameSingular).".service';\n";
        $content .= "import { environment } from 'src/environments/environment';\n";
        $activarMapaGoogle = false;
        foreach ($tableColumns as $column) {
            if ($column['type']==='gmap') {
                $activarMapaGoogle = true;
            }
        }
        if($activarMapaGoogle) {
            $content .= "import { AgmCoreModule } from '@agm/core';\n";
        }
        foreach ($relationships as $relationship) {
            if ($bddType == "SQL") {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['fromSingular'] === 'User') {
                        $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/profile/".strtolower($relationship['fromSingular']).".service';\n";
                    } else {
                        if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                            $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['fromSingular']).".service';\n";
                        }
                    }
                }
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['toSingular'] === 'User') {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/profile/".strtolower($relationship['toSingular']).".service';\n";
                    } else {
                        if ($relationship['kind'] === 'many2one') {
                            $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['toSingular']).".service';\n";
                        }
                    }
                }
            } else {
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['toSingular'] === 'User') {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/profile/".strtolower($relationship['toSingular']).".service';\n";
                    } else {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['toSingular']).".service';\n";
                    }
                }
            }
        }
        $isLongText = false;
        foreach ($tableColumns as $column) {
            if ($column['group']==="fillable") {
                if ($column['type']==='longText') {
                    $isLongText = true;
                }
            }
        }
        if ($isLongText && !$esAdjunto) {
            $content .= "import { CKEditorModule } from 'ngx-ckeditor';\n";
        }
        $content .= "\n@NgModule({\n";
        $content .= "   imports: [CommonModule,\n";
        $content .= "             ".$tableNameSingular."RoutingModule,\n";
        if ($isLongText && !$esAdjunto) {
            $content .= "             CKEditorModule,\n";
        }
        if($activarMapaGoogle) {
            $content .= "             AgmCoreModule.forRoot({apiKey: environment.gmapapiKey}),\n";

        }
        $content .= "             FormsModule],\n";
        $content .= "   declarations: [".$tableNameSingular."Component],\n";
        $content .= "   providers: [\n";
        if($type === 'bootstrap'){
            $content .= "               NgbModal,\n";
        }
        foreach ($relationships as $relationship) {
            if ($bddType == "SQL") {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                        $content .= "               ".$relationship['fromSingular']."Service,\n";
                    }
                }
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2one') {
                        $content .= "               ".$relationship['toSingular']."Service,\n";
                    }
                }
            } else {
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    $content .= "               ".$relationship['toSingular']."Service,\n";
                }
            }
        }
        $content .= "               ".$tableNameSingular."Service\n";
        $content .= "               ]\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."Module {}";
        return $content;
    }
}
