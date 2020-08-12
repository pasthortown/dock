<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class ComponentTSTemplateController extends Controller
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
        $tableNamePlural = $args['Table']['namePlural'];
        $relationships = $args['RelationShip'];
        $tableColumns = $args['Columns'];
        $esAdjunto = $args['esAdjunto'];
        $content = "import { Component, OnInit } from '@angular/core';\n";
        if($type === 'bootstrap') {
            $content .= "import { NgbModal } from '@ng-bootstrap/ng-bootstrap';\n";
        }
        $content .= "import { ToastrManager } from 'ng6-toastr-notifications';\n";
        $content .= "import { saveAs } from 'file-saver/FileSaver';\n";
        $content .= "import { ".$tableNameSingular."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($tableNameSingular).".service';\n";
        $content .= "import { ".$tableNameSingular." } from './../../../../models/".strtoupper($moduleName)."/".$tableNameSingular."';\n";
        foreach ($relationships as $relationship) {
            if ($bddType == "SQL") {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['fromSingular'] === 'User') {
                        $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/profile/".strtolower($relationship['fromSingular']).".service';\n";
                        $content .= "import { ".$relationship['fromSingular']." } from './../../../../models/profile/".$relationship['fromSingular']."';\n\n";
                    } else {
                        if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                            $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['fromSingular']).".service';\n";
                            $content .= "import { ".$relationship['fromSingular']." } from './../../../../models/".strtoupper($moduleName)."/".$relationship['fromSingular']."';\n\n";
                        }
                    }
                }
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['toSingular'] === 'User') {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/profile/".strtolower($relationship['toSingular']).".service';\n";
                        $content .= "import { ".$relationship['toSingular']." } from './../../../../models/profile/".$relationship['toSingular']."';\n\n";
                    } else {
                        if ($relationship['kind'] === 'many2one') {
                            $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['toSingular']).".service';\n";
                            $content .= "import { ".$relationship['toSingular']." } from './../../../../models/".strtoupper($moduleName)."/".$relationship['toSingular']."';\n";
                        }
                    }
                }
            } else {
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['toSingular'] === 'User') {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/profile/".strtolower($relationship['toSingular']).".service';\n";
                        $content .= "import { ".$relationship['toSingular']." } from './../../../../models/profile/".$relationship['toSingular']."';\n\n";
                    } else {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['toSingular']).".service';\n";
                        $content .= "import { ".$relationship['toSingular']." } from './../../../../models/".strtoupper($moduleName)."/".$relationship['toSingular']."';\n";
                    }
                }
            }
        }
        $content .= "\n@Component({\n";
        $content .= "   selector: 'app-".strtolower($tableNameSingular)."',\n";
        $content .= "   templateUrl: './".strtolower($tableNameSingular).".component.html',\n";
        $content .= "   styleUrls: ['./".strtolower($tableNameSingular).".component.scss']\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."Component implements OnInit {\n";
        $content .= "   ".UtilitiesController::checkNames($tableNamePlural).": ".$tableNameSingular."[] = [];\n";
        $content .= "   ".UtilitiesController::checkNames($tableNameSingular)."Selected: ".$tableNameSingular." = new ".$tableNameSingular."();\n\n";
        $content .= "   currentPage = 1;\n";
        $content .= "   lastPage = 1;\n";
        $content .= "   showDialog = false;\n";
        $content .= "   recordsByPage = 5;\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "   ".UtilitiesController::checkNames($relationship['fromPlural']).": ".$relationship['fromSingular']."[] = [];\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "   ".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId: number;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   ".UtilitiesController::checkNames($relationship['toPlural']).": ".$relationship['toSingular']."[] = [];\n";
                }
            }
        }
        $content .= "   constructor(\n";
        if($type === 'bootstrap') {
            $content .= "               private modalService: NgbModal,\n";
        }
        $content .= "               private toastr: ToastrManager,\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "               private ".UtilitiesController::checkNames($relationship['fromSingular'])."DataService: ".$relationship['fromSingular']."Service,\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "               private ".UtilitiesController::checkNames($relationship['toSingular'])."DataService: ".$relationship['toSingular']."Service,\n";
                }
            }
        }
        $content .= "               private ".UtilitiesController::checkNames($tableNameSingular)."DataService: ".$tableNameSingular."Service) {}\n\n";
        $content .= "   ngOnInit() {\n";
        $content .= "      this.goToPage(1);\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "      this.get".$relationship['fromSingular']."();\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "      this.get".$relationship['toSingular']."();\n";
                }
            }
        }
        $content .= "   }\n\n";
        if ($esAdjunto) {
            $content .= "   CodeFile".$tableNameSingular."(event) {\n";
            $content .= "      const reader = new FileReader();\n";
            $content .= "      if (event.target.files && event.target.files.length > 0) {\n";
            $content .= "         const file = event.target.files[0];\n";
            $content .= "         reader.readAsDataURL(file);\n";
            $content .= "         reader.onload = () => {\n";
            $content .= "            this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($tableNameSingular."FileName")." = file.name;\n";
            $content .= "            this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($tableNameSingular."FileType")." = file.type;\n";
            $content .= "            this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($tableNameSingular."File")." = reader.result.toString().split(',')[1];\n";
            $content .= "         };\n";
            $content .= "      }\n";
            $content .= "   }\n\n";
        }
        $content .= "   select".$tableNameSingular."(".UtilitiesController::checkNames($tableNameSingular).": ".$tableNameSingular.") {\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected = ".UtilitiesController::checkNames($tableNameSingular).";\n";
        $content .= "   }\n\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "   get".$relationship['fromSingular']."() {\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])." = [];\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromSingular'])."DataService.get().then( r => {\n";
                    $content .= "         this.".UtilitiesController::checkNames($relationship['fromPlural'])." = r as ".$relationship['fromSingular']."[];\n";
                    $content .= "      }).catch( e => console.log(e) );\n";
                    $content .= "   }\n\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "   get".$relationship['fromPlural']."On".$relationship['toSingular']."() {\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = [];\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."DataService.get(this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id).then( r => {\n";
                    $content .= "         this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = r.attach[0].".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as ".$relationship['fromSingular']."[];\n";
                    $content .= "      }).catch( e => console.log(e) );\n";
                    $content .= "   }\n\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   get".$relationship['toSingular']."() {\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['toPlural'])." = [];\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['toSingular'])."DataService.get().then( r => {\n";
                    $content .= "         this.".UtilitiesController::checkNames($relationship['toPlural'])." = r as ".$relationship['toSingular']."[];\n";
                    $content .= "      }).catch( e => console.log(e) );\n";
                    $content .= "   }\n\n";
                }
            }
        }
        $content .= "   goToPage(page: number) {\n";
        $content .= "      if ( page < 1 || page > this.lastPage ) {\n";
        $content .= "         this.toastr.errorToastr('La página solicitada no existe.', 'Error');\n";
        $content .= "         return;\n";
        $content .= "      }\n";
        $content .= "      this.currentPage = page;\n";
        $content .= "      this.get".$tableNamePlural."();\n";
        $content .= "   }\n\n";
        foreach ($tableColumns as $column) {
            if ($column['type']==='gmap') {
                $content .= "   ".UtilitiesController::checkNames($column['name'])."Event(event) {\n";
                if ($bddType == "SQL") {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_latitude = event.coords.lat;\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_longitude = event.coords.lng;\n";
                } else {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[1] = event.coords.lat;\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[0] = event.coords.lng;\n";
                }
                $content .= "   }\n\n";
            }
        }
        $content .= "   get".$tableNamePlural."() {\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNamePlural)." = [];\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected = new ".$tableNameSingular."();\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromSingular'])."_id = 0;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['toSingular'])."_id = 0;\n";
                }
            }
        }
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."DataService.get_paginate(this.recordsByPage, this.currentPage).then( r => {\n";
        $content .= "         this.".UtilitiesController::checkNames($tableNamePlural)." = r.data as ".$tableNameSingular."[];\n";
        $content .= "         this.lastPage = r.last_page;\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   new".$tableNameSingular."(content) {\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected = new ".$tableNameSingular."();\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromSingular'])."_id = 0;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['toSingular'])."_id = 0;\n";
                }
            }
        }
        if($type === 'bootstrap'){
            $content .= "      this.openDialog(content);\n";
        }
        if($type === 'metro'){
            $content .= "      this.showDialog = true;\n";
        }
        $content .= "   }\n\n";
        $content .= "   edit".$tableNameSingular."(content) {\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      if ( typeof this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." === 'undefined' ) {\n";
                    $content .= "         this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = [];\n";
                    $content .= "      }\n";
                }
            }
        }
        $content .= "      if (typeof this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
        $content .= "         this.toastr.errorToastr('Debe seleccionar un registro.', 'Error');\n";
        $content .= "         return;\n";
        $content .= "      }\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.get".$relationship['fromPlural']."On".$relationship['toSingular']."();\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                }
            }
        }
        if($type === 'bootstrap'){
            $content .= "      this.openDialog(content);\n";
        }
        if($type === 'metro'){
            $content .= "      this.showDialog = true;\n";
        }
        $content .= "   }\n\n";
        $content .= "   delete".$tableNameSingular."() {\n";
        $content .= "      if (typeof this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
        $content .= "         this.toastr.errorToastr('Debe seleccionar un registro.', 'Error');\n";
        $content .= "         return;\n";
        $content .= "      }\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."DataService.delete(this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id).then( r => {\n";
        $content .= "         this.toastr.successToastr('Registro Borrado satisfactoriamente.', 'Borrar');\n";
        $content .= "         this.get".$tableNamePlural."();\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   backup() {\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."DataService.getBackUp().then( r => {\n";
        $content .= "         const backupData = r;\n";
        $content .= "         const blob = new Blob([JSON.stringify(backupData)], { type: 'text/plain;charset=utf-8' });\n";
        $content .= "         const fecha = new Date();\n";
        $content .= "         saveAs(blob, fecha.toLocaleDateString() + '_".$tableNamePlural.".json');\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   toCSV() {\n";
        $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."DataService.get().then( r => {\n";
        $content .= "         const backupData = r as ".$tableNameSingular."[];\n";
        $colsHeader = "id;";
        $colsRows = "element.id + ';' + ";
        foreach ($tableColumns as $column) {
            if($column['type']==="gmap") {
                if ($bddType == "SQL") {
                    $colsHeader .= UtilitiesController::checkNames($column['name'])."_latitude;";
                    $colsHeader .= UtilitiesController::checkNames($column['name'])."_longitude;";
                    $colsRows .= "element.".UtilitiesController::checkNames($column['name'])."_latitude + ';' + ";
                    $colsRows .= "element.".UtilitiesController::checkNames($column['name'])."_longitude + ';' + ";
                } else {
                    $colsHeader .= UtilitiesController::checkNames($column['name']).".coordinates[1];";
                    $colsHeader .= UtilitiesController::checkNames($column['name']).".coordinates[0];";
                    $colsRows .= "element.".UtilitiesController::checkNames($column['name']).".coordinates[1] + ';' + ";
                    $colsRows .= "element.".UtilitiesController::checkNames($column['name']).".coordinates[0] + ';' + ";
                }
            }else {
                $colsHeader .= UtilitiesController::checkNames($column['name']).";";
                $colsRows .= "element.".UtilitiesController::checkNames($column['name'])." + ';' + ";
            }
        }
        foreach ($relationships as $relationship) {
            if ($relationship['kind'] !== 'many2many') {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($bddType == "SQL") {
                        $colsHeader .= "". UtilitiesController::checkNames($relationship['fromSingular']) ."_id;";
                        $colsRows .= "element.". UtilitiesController::checkNames($relationship['fromSingular']) ."_id + ';' + ";
                    }
                }
            }
        }
        $colsHeader = trim($colsHeader,";");
        $colsRows = trim($colsRows," + ';' + ");
        $content .= "         let output = '".$colsHeader."\\n';\n";
        $content .= "         backupData.forEach(element => {\n";
        $content .= "            output += ".$colsRows." + '\\n';\n";
        $content .= "         });\n";
        $content .= "         const blob = new Blob([output], { type: 'text/plain;charset=utf-8' });\n";
        $content .= "         const fecha = new Date();\n";
        $content .= "         saveAs(blob, fecha.toLocaleDateString() + '_".$tableNamePlural.".csv');\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   decodeUploadFile(event) {\n";
        $content .= "      const reader = new FileReader();\n";
        $content .= "      if (event.target.files && event.target.files.length > 0) {\n";
        $content .= "         const file = event.target.files[0];\n";
        $content .= "         reader.readAsDataURL(file);\n";
        $content .= "         reader.onload = () => {\n";
        $content .= "            const fileBytes = reader.result.toString().split(',')[1];\n";
        $content .= "            const newData = JSON.parse(decodeURIComponent(escape(atob(fileBytes)))) as any[];\n";
        $content .= "            this.".UtilitiesController::checkNames($tableNameSingular)."DataService.masiveLoad(newData).then( r => {\n";
        $content .= "               this.goToPage(this.currentPage);\n";
        $content .= "            }).catch( e => console.log(e) );\n";
        $content .= "         };\n";
        $content .= "      }\n";
        $content .= "   }\n\n";
        if ($esAdjunto) {
            $content .= "   downloadFile(file: string, type: string, name: string) {\n";
            $content .= "      const byteCharacters = atob(file);\n";
            $content .= "      const byteNumbers = new Array(byteCharacters.length);\n";
            $content .= "      for (let i = 0; i < byteCharacters.length; i++) {\n";
            $content .= "         byteNumbers[i] = byteCharacters.charCodeAt(i);\n";
            $content .= "      }\n";
            $content .= "      const byteArray = new Uint8Array(byteNumbers);\n";
            $content .= "      const blob = new Blob([byteArray], { type: type});\n";
            $content .= "      saveAs(blob, name);\n";
            $content .= "   }\n\n";
        }
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "   select".$relationship['fromSingular']."(".UtilitiesController::checkNames($relationship['fromSingular']).": ".$relationship['fromSingular'].") {\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = ".UtilitiesController::checkNames($relationship['fromSingular']).".id;\n";
                    $content .= "   }\n\n";
                    $content .= "   add".$relationship['fromSingular']."() {\n";
                    $content .= "      if (this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId === 0) {\n";
                    $content .= "         this.toastr.errorToastr('Seleccione un registro.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural']).".forEach(".UtilitiesController::checkNames($relationship['fromSingular'])." => {\n";
                    $content .= "         if (".UtilitiesController::checkNames($relationship['fromSingular']).".id == this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId) {\n";
                    $content .= "            let existe = false;\n";
                    $content .= "            this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular']).".forEach(element => {\n";
                    $content .= "               if (element.id == ".UtilitiesController::checkNames($relationship['fromSingular']).".id) {\n";
                    $content .= "                  existe = true;\n";
                    $content .= "               }\n";
                    $content .= "            });\n";
                    $content .= "            if (!existe) {\n";
                    $content .= "               this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular']).".push(".UtilitiesController::checkNames($relationship['fromSingular']).");\n";
                    $content .= "               this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                    $content .= "            } else {\n";
                    $content .= "               this.toastr.errorToastr('El registro ya existe.', 'Error');\n";
                    $content .= "            }\n";
                    $content .= "         }\n";
                    $content .= "      });\n";
                    $content .= "   }\n\n";
                    $content .= "   remove".$relationship['fromSingular']."() {\n";
                    $content .= "      if (this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId === 0) {\n";
                    $content .= "         this.toastr.errorToastr('Seleccione un registro.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      const new".$relationship['fromPlural'].": ".$relationship['fromSingular']."[] = [];\n";
                    $content .= "      let eliminado = false;\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular']).".forEach(".UtilitiesController::checkNames($relationship['fromSingular'])." => {\n";
                    $content .= "         if (".UtilitiesController::checkNames($relationship['fromSingular']).".id !== this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId) {\n";
                    $content .= "            new".$relationship['fromPlural'].".push(".UtilitiesController::checkNames($relationship['fromSingular']).");\n";
                    $content .= "         } else {\n";
                    $content .= "            eliminado = true;\n";
                    $content .= "         }\n";
                    $content .= "      });\n";
                    $content .= "      if (!eliminado) {\n";
                    $content .= "         this.toastr.errorToastr('Registro no encontrado.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      this.".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = new".$relationship['fromPlural'].";\n";
                    $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                    $content .= "   }\n\n";
                }
            }
        }
        if($type === 'bootstrap'){
            $content .= "   openDialog(content) {\n";
            $isLongText = false;
            foreach ($tableColumns as $column) {
                if ($column['group']==="fillable") {
                    if ($column['type']==='longText') {
                        $isLongText = true;
                    }
                }
            }
            $sizeModal = " ";
            if ($isLongText) {
                $sizeModal .= ", size: 'lg' ";
            }
            $content .= "      this.modalService.open(content, { centered: true".$sizeModal."}).result.then(( response => {\n";
            $content .= "         if ( response === 'Guardar click' ) {\n";
            $content .= "            if (typeof this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
            $content .= "               this.".UtilitiesController::checkNames($tableNameSingular)."DataService.post(this.".UtilitiesController::checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "                  this.toastr.successToastr('Datos guardados satisfactoriamente.', 'Nuevo');\n";
            $content .= "                  this.get".$tableNamePlural."();\n";
            $content .= "               }).catch( e => console.log(e) );\n";
            $content .= "            } else {\n";
            $content .= "               this.".UtilitiesController::checkNames($tableNameSingular)."DataService.put(this.".UtilitiesController::checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "                  this.toastr.successToastr('Registro actualizado satisfactoriamente.', 'Actualizar');\n";
            $content .= "                  this.get".$tableNamePlural."();\n";
            $content .= "               }).catch( e => console.log(e) );\n";
            $content .= "            }\n";
            $content .= "         }\n";
            $content .= "      }), ( r => {}));\n";
            $content .= "   }\n";
        }
        if($type === 'metro'){
            $content .= "   saveDialogResult() {\n";
            $content .= "      if (typeof this.".UtilitiesController::checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
            $content .= "         this.".UtilitiesController::checkNames($tableNameSingular)."DataService.post(this.".UtilitiesController::checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "            this.toastr.successToastr('Datos guardados satisfactoriamente.', 'Nuevo');\n";
            $content .= "            this.get".$tableNamePlural."();\n";
            $content .= "         }).catch( e => console.log(e) );\n";
            $content .= "      } else {\n";
            $content .= "         this.".UtilitiesController::checkNames($tableNameSingular)."DataService.put(this.".UtilitiesController::checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "            this.toastr.successToastr('Registro actualizado satisfactoriamente.', 'Actualizar');\n";
            $content .= "            this.get".$tableNamePlural."();\n";
            $content .= "         }).catch( e => console.log(e) );\n";
            $content .= "      }\n";
            $content .= "   }\n\n";
            $content .= "   cancelDialogResult() {\n";
            $content .= "      this.showDialog = false;\n";
            $content .= "      this.goToPage(this.currentPage);\n";
            $content .= "   }\n";
        }
        $content .= "}";
        return $content;
    }
}
