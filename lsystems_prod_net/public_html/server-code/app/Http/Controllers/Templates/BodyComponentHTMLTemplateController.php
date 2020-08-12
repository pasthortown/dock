<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class BodyComponentHTMLTemplateController extends Controller
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

    static function buildMetro($args, $bddType) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $esAdjunto = $args['esAdjunto'];
        $content = "<div class=\"row\">\n";
        $content .= "   <h1 class=\"cell-12 text-right\">\n";
        $content .= "      ".$tableNameSingular."\n";
        $content .= "   </h1>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"cell-12\">\n";
        $content .= "      <div class=\"toolbar\">\n";
        $content .= "         <button class=\"tool-button primary\" title=\"Actualizar\" (click)=\"goToPage(currentPage)\"><i class=\"fas fa-sync\"></i></button>\n";
        $content .= "         <button class=\"tool-button success ml-2\" title=\"Nuevo\" (click)=\"new".$tableNameSingular."()\"><i class=\"fas fa-file\"></i></button>\n";
        $content .= "         <button class=\"tool-button warning\" title=\"Editar\" (click)=\"edit".$tableNameSingular."()\"><i class=\"fas fa-edit\"></i></button>\n";
        $content .= "         <button class=\"tool-button alert ml-2\" title=\"Eliminar\" (click)=\"delete".$tableNameSingular."()\"><i class=\"fas fa-trash\"></i></button>\n";
        $content .= "         <button class=\"tool-button dark ml-2\" title=\"BackUp\" (click)=\"backup()\"><i class=\"fas fa-download\"></i></button>\n";
        $content .= "         <button class=\"tool-button dark\" title=\"Exportar CSV\" (click)=\"toCSV()\"><i class=\"fas fa-file-csv\"></i></button>\n";
        $content .= "         <button class=\"tool-button dark\" title=\"Cargar\" (click)=\"uploadInput.click()\"><i class=\"fas fa-upload\"></i></button>\n";
        $content .= "         <input [hidden]=\"true\" type=\"file\" class=\"form-control\" #uploadInput (change)=\"decodeUploadFile(\$event)\" accept=\".json\"/>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"cell-12\">\n";
        $content .= "      <table class=\"table row-hover mt-2\">\n";
        $content .= "         <thead>\n";
        $content .= "            <tr>\n";
        $content .= "               <th>Seleccionado</th>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                $content .= "               <th>".UtilitiesController::checkNames($column['name'])."</th>\n";
            }
        }
        if ($esAdjunto) {
            $content .= "               <th>Opciones</th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </thead>\n";
        $content .= "         <tbody>\n";
        $content .= "            <tr *ngFor=\"let ".UtilitiesController::checkNames($tableNameSingular)." of ".UtilitiesController::checkNames($tableNamePlural)."\" (click)=\"select".$tableNameSingular."(".UtilitiesController::checkNames($tableNameSingular).")\">\n";
        $content .= "               <td class=\"text-right\"><span *ngIf=\"".UtilitiesController::checkNames($tableNameSingular)."Selected === ".UtilitiesController::checkNames($tableNameSingular)."\" class=\"far fa-hand-point-right\"></span></td>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if($column['type']==="gmap") {
                    if ($bddType == "SQL") {
                        $content .= "               <td>Lat: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."_latitude}} Lng: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."_longitude}}</td>\n";
                    } else {
                        $content .= "               <td>Lat: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name']).".coordinates[1]}} Lng: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name']).".coordinates[0]}}</td>\n";
                    }
                }else {
                    $content .= "               <td>{{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."}}</td>\n";
                }
            }
        }
        if ($esAdjunto) {
            $content .= "               <th><button type=\"button\" title=\"Descargar\" class=\"button success\" (click)=\"downloadFile(".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file, ".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file_type, ".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file_name)\"><i class=\"fas fa-download\"></i></button></th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </tbody>\n";
        $content .= "      </table>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"cell-12\">\n";
        $content .= "      <div class=\"toolbar\">\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage === 1\" title=\"Primera Página\" disabled>Primera</button>\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage !== 1\" title=\"Primera Página\" (click)=\"goToPage(1)\">Primera</button>\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage > 1\" title=\"Página Anterior\" (click)=\"goToPage((currentPage * 1) - 1)\">{{(currentPage * 1) - 1}}</button>\n";
        $content .= "         <button type=\"button\" class=\"button primary\" title=\"Página Actual\">{{currentPage}}</button>\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage < lastPage\" title=\"Página Siguiente\" (click)=\"goToPage((currentPage * 1) + 1)\">{{(currentPage * 1) + 1}}</button>\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage !== lastPage\" title=\"Última Página\" (click)=\"goToPage(lastPage)\">Última</button>\n";
        $content .= "         <button type=\"button\" class=\"button light\" *ngIf=\"currentPage === lastPage\" title=\"Última Página\" disabled>Última</button>\n";
        $content .= "         <button type=\"button\" class=\"button success ml-2\" title=\"Ir a la Página\" (click)=\"goToPage(goToPageNumber.value)\">Ir a</button>\n";
        $content .= "         <input type=\"number\" min=\"{{1}}\" max=\"{{lastPage}}\" placeholder=\"Ir a la Página\" #goToPageNumber>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\" *ngIf=\"showDialog\">\n";
        $content .= "   <div class=\"cell-12 mt-5\">\n";
        $content .= "      <div class=\"window\" data-role=\"window\" data-icon=\"<span class='mif-pencil'></span>\" data-title=\"Datos:\" data-btn-close=\"false\" data-btn-min=\"false\" data-btn-max=\"false\" data-width=\"800\" data-shadow=\"true\" data-place=\"top-center\" data-resizable=\"false\" data-draggable=\"false\">\n";
        $content .= "         <div class=\"window-content m-2\">\n";
        $content .= "            <div class=\"container\">\n";
        $content .= "               <div class=\"row\">\n";
        $content .= "                  <div class=\"cell-12\">\n";
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                $content .= "                     <div class=\"form-group row\">\n";
                $content .= "                        <label for=\"".UtilitiesController::checkNames($column['name'])."\">".UtilitiesController::checkNames($column['name'])."</label>\n";
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "                        <input type=\"date\" data-role=\"input\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [ngModel]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])." | date:'y-MM-dd'\" (ngModelChange)=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])." = \$event\">\n";
                } else {
                    if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                        $content .= "                        <input type=\"number\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\">\n";
                    } else {
                        if ($column['type']==='longText' && !$esAdjunto) {
                            $content .= "                        <ck-editor id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" skin=\"moono-lisa\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\"></ck-editor>\n";
                        } else {
                            if ($column['type']==='longText' && $esAdjunto) {
                                $content .= "                        <input type=\"file\" data-role=\"file\" data-button-title=\"<span class='mif-folder'></span>\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" (change)=\"CodeFile".$tableNameSingular."(\$event)\">\n";
                            } else {
                                if ($column['type']==='boolean') {
                                    $content .= "                        <input type=\"checkbox\" data-role=\"switch\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\">\n";
                                } else {
                                    if ($column['type']==='gmap') {
                                        if ($bddType == "SQL") {
                                            $content .= "                        <agm-map class=\"cell-12\" style=\"height: 200px;\"[latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_latitude * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_longitude * 1\" [zoom]=\"15\" (mapClick)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\">\n";
                                            $content .= "                           <agm-marker [latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_latitude * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_longitude * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                            $content .= "                        </agm-map>\n";
                                        } else {
                                            $content .= "                        <agm-map class=\"cell-12\" style=\"height: 200px;\"[latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[1] * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[0] * 1\" [zoom]=\"15\" (mapClick)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\">\n";
                                            $content .= "                           <agm-marker [latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[1] * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[0] * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                            $content .= "                        </agm-map>\n";
                                        }
                                    } else {
                                        $content .= "                        <input type=\"text\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\">\n";
                                    }
                                }
                            }
                        }
                    }
                }
                $content .= "                     </div>\n";
            }
        }
        $relationships = $args['RelationShip'];
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label for=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromSingular'])."_id\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($relationship['fromPlural'])."\" value={{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                              {{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                           </option>\n";
                    $content .= "                        </select>\n";
                    $content .= "                     </div>\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label class=\"cell-12\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($relationship['fromPlural'])."\" value={{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                              {{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                           </option>\n";
                    $content .= "                        </select>\n";
                    $content .= "                     </div>\n";
                    $content .= "                     <div class=\"row\">\n";
                    $content .= "                        <div class=\"cell-12 text-center\">\n";
                    $content .= "                           <button type=\"button\" title=\"Eliminar\" class=\"button alert\" (click)=\"remove".$relationship['fromSingular']."()\"><i class=\"fas fa-trash\"></i></button>\n";
                    $content .= "                           <button type=\"button\" title=\"Agregar\" class=\"button success\" (click)=\"add".$relationship['fromSingular']."()\"><i class=\"fas fa-plus-circle\"></i></button>\n";
                    $content .= "                        </div>\n";
                    $content .= "                     </div>\n";
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label class=\"cell-12\"><strong>".$relationship['fromPlural']."</strong></label>\n";
                    $content .= "                        <table class=\"table row-hover mt-2\">\n";
                    $content .= "                           <tbody>\n";
                    $content .= "                              <tr *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."\" (click)=\"select".$relationship['fromSingular']."(".UtilitiesController::checkNames($relationship['fromSingular']).")\">\n";
                    $content .= "                                 <td class=\"text-right\"><span *ngIf=\"".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId === ".UtilitiesController::checkNames($relationship['fromSingular']).".id\" class=\"far fa-hand-point-right\"></span></td>\n";
                    $content .= "                                 <td>{{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}</td>\n";
                    $content .= "                              </tr>\n";
                    $content .= "                           </tbody>\n";
                    $content .= "                        </table>\n";
                    $content .= "                     </div>\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label for=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".UtilitiesController::checkNames($relationship['toSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['toSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['toSingular'])."_id\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['toSingular'])." of ".UtilitiesController::checkNames($relationship['toPlural'])."\" value={{".UtilitiesController::checkNames($relationship['toSingular']).".id}}>\n";
                    $content .= "                              {{".UtilitiesController::checkNames($relationship['toSingular']).".id}}\n";
                    $content .= "                           </option>\n";
                    $content .= "                        </select>\n";
                    $content .= "                     </div>\n";
                }
            }
        }
        $content .= "                  </div>\n";
        $content .= "               </div>\n";
        $content .= "               <div class=\"row mt-2\">\n";
        $content .= "                  <div class=\"cell text-center\">\n";
        $content .= "                     <button type=\"button\" class=\"button success\" (click)=\"saveDialogResult()\">Guardar</button>\n";
        $content .= "                     <button type=\"button\" class=\"button alert\" (click)=\"cancelDialogResult()\">Cancelar</button>\n";
        $content .= "                  </div>\n";
        $content .= "               </div>\n";
        $content .= "            </div>\n";
        $content .= "         </div>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "</div>";
        return $content;
    }

    static function buildBootstrap($args, $bddType) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $esAdjunto = $args['esAdjunto'];
        $content = "<div class=\"row\">\n";
        $content .= "   <h1 class=\"col-12 text-right\">\n";
        $content .= "      ".$tableNameSingular."\n";
        $content .= "   </h1>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"col-12\">\n";
        $content .= "      <div class=\"btn-toolbar\" role=\"toolbar\">\n";
        $content .= "         <div class=\"btn-group mr-2\" role=\"group\">\n";
        $content .= "            <button type=\"button\" class=\"btn btn-primary\" title=\"Actualizar\" (click)=\"goToPage(currentPage)\"><i class=\"fas fa-sync\"></i></button>\n";
        $content .= "         </div>\n";
        $content .= "         <div class=\"btn-group mr-2\" role=\"group\">\n";
        $content .= "            <button type=\"button\" title=\"Nuevo\" class=\"btn btn-success\" (click)=\"new".$tableNameSingular."(content)\"><i class=\"fas fa-file\"></i></button>\n";
        $content .= "            <button type=\"button\" title=\"Editar\" class=\"btn btn-warning\" (click)=\"edit".$tableNameSingular."(content)\"><i class=\"fas fa-edit\"></i></button>\n";
        $content .= "         </div>\n";
        $content .= "         <div class=\"btn-group mr-2\" role=\"group\">\n";
        $content .= "            <button type=\"button\" title=\"Eliminar\" class=\"btn btn-danger\" (click)=\"delete".$tableNameSingular."()\"><i class=\"fas fa-trash\"></i></button>\n";
        $content .= "         </div>\n";
        $content .= "         <div class=\"btn-group mr-2\" role=\"group\">\n";
        $content .= "            <button type=\"button\" title=\"BackUp\" class=\"btn btn-dark\" (click)=\"backup()\"><i class=\"fas fa-download\"></i></button>\n";
        $content .= "            <button type=\"button\" title=\"Exportar CSV\" class=\"btn btn-dark\" (click)=\"toCSV()\"><i class=\"fas fa-file-csv\"></i></button>\n";
        $content .= "            <button type=\"button\" title=\"Cargar\" class=\"btn btn-dark\" (click)=\"uploadInput.click()\"><i class=\"fas fa-upload\"></i></button>\n";
        $content .= "            <input [hidden]=\"true\" type=\"file\" class=\"form-control\" #uploadInput (change)=\"decodeUploadFile(\$event)\" accept=\".json\"/>\n";
        $content .= "         </div>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"col-12\">\n";
        $content .= "      <table class=\"table table-hover mt-2\">\n";
        $content .= "         <thead>\n";
        $content .= "            <tr>\n";
        $content .= "               <th>Seleccionado</th>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                $content .= "               <th>".UtilitiesController::checkNames($column['name'])."</th>\n";
            }
        }
        if ($esAdjunto) {
            $content .= "               <th>Opciones</th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </thead>\n";
        $content .= "         <tbody>\n";
        $content .= "            <tr *ngFor=\"let ".UtilitiesController::checkNames($tableNameSingular)." of ".UtilitiesController::checkNames($tableNamePlural)."\" (click)=\"select".$tableNameSingular."(".UtilitiesController::checkNames($tableNameSingular).")\">\n";
        $content .= "               <td class=\"text-right\"><span *ngIf=\"".UtilitiesController::checkNames($tableNameSingular)."Selected === ".UtilitiesController::checkNames($tableNameSingular)."\" class=\"far fa-hand-point-right\"></span></td>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if($column['type']==="gmap") {
                    if ($bddType == "SQL") {
                        $content .= "               <td>Lat: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."_latitude}} Lng: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."_longitude}}</td>\n";
                    } else {
                        $content .= "               <td>Lat: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name']).".coordinates[1]}} Lng: {{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name']).".coordinates[0]}}</td>\n";
                    }
                }else {
                    $content .= "               <td>{{".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($column['name'])."}}</td>\n";
                }
            }
        }
        if ($esAdjunto) {
            $content .= "               <th><button type=\"button\" title=\"Descargar\" class=\"btn btn-success\" (click)=\"downloadFile(".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file, ".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file_type, ".UtilitiesController::checkNames($tableNameSingular).".".UtilitiesController::checkNames($tableNameSingular)."_file_name)\"><i class=\"fas fa-download\"></i></button></th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </tbody>\n";
        $content .= "      </table>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<div class=\"row\">\n";
        $content .= "   <div class=\"col-12\">\n";
        $content .= "      <div class=\"btn-toolbar\" role=\"toolbar\">\n";
        $content .= "         <div class=\"btn-group mr-2\" role=\"group\">\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage === 1\" title=\"Primera Página\" disabled>Primera</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage !== 1\" title=\"Primera Página\" (click)=\"goToPage(1)\">Primera</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage > 1\" title=\"Página Anterior\" (click)=\"goToPage((currentPage*1) - 1)\">{{(currentPage * 1) - 1}}</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-primary\" title=\"Página Actual\">{{currentPage}}</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage < lastPage\" title=\"Página Siguiente\" (click)=\"goToPage((currentPage*1) + 1)\">{{(currentPage * 1) + 1}}</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage !== lastPage\" title=\"Última Página\" (click)=\"goToPage(lastPage)\">Última</button>\n";
        $content .= "            <button type=\"button\" class=\"btn btn-light\" *ngIf=\"currentPage === lastPage\" title=\"Última Página\" disabled>Última</button>\n";
        $content .= "         </div>\n";
        $content .= "         <div class=\"input-group\">\n";
        $content .= "            <div class=\"input-group-prepend\">\n";
        $content .= "               <button type=\"button\" class=\"input-group-text btn btn-success\" title=\"Ir a la Página\" (click)=\"goToPage(goToPageNumber.value)\">Ir a</button>\n";
        $content .= "            </div>\n";
        $content .= "            <input type=\"number\" min=\"{{1}}\" max=\"{{lastPage}}\" class=\"form-control\" placeholder=\"Ir a la Página\" #goToPageNumber>\n";
        $content .= "         </div>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "</div>\n";
        $content .= "<ng-template #content let-modal>\n";
        $content .= "   <div class=\"modal-header\">\n";
        $content .= "      <h4 class=\"modal-title\">Datos:</h4>\n";
        $content .= "      <button type=\"button\" class=\"close\" (click)=\"modal.dismiss('Cross click')\">\n";
        $content .= "         <span>&times;</span>\n";
        $content .= "      </button>\n";
        $content .= "   </div>\n";
        $content .= "   <div class=\"modal-body\">\n";
        $content .= "      <div class=\"row\">\n";
        $content .= "         <div class=\"col-12\">\n";
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                $content .= "            <div class=\"form-group row\">\n";
                $content .= "               <label for=\"".UtilitiesController::checkNames($column['name'])."\" class=\"col-4 col-form-label\">".UtilitiesController::checkNames($column['name'])."</label>\n";
                $content .= "               <div class=\"col-8\">\n";
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "                  <input type=\"date\" class=\"form-control\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [ngModel]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])." | date:'y-MM-dd'\" (ngModelChange)=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])." = \$event\">\n";
                } else {
                    if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                        $content .= "                  <input type=\"number\" class=\"form-control\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\">\n";
                    } else {
                        if ($column['type']==='longText' && !$esAdjunto) {
                            $content .= "                  <ck-editor id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" skin=\"moono-lisa\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\"></ck-editor>\n";
                        } else {
                            if ($column['type']==='longText' && $esAdjunto) {
                                $content .= "                  <input type=\"file\" class=\"form-control\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" (change)=\"CodeFile".$tableNameSingular."(\$event)\">\n";
                            } else {
                                if ($column['type']==='boolean') {
                                    $content .= "                  <label class=\"switch\"><input type=\"checkbox\"id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\"><span class=\"slider round\"></span></label>\n";
                                } else {
                                    if ($column['type']==='gmap') {
                                        if ($bddType == "SQL") {
                                            $content .= "                  <agm-map class=\"col-12\" style=\"height: 200px;\"[latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_latitude * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_longitude * 1\" [zoom]=\"15\" (mapClick)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\">\n";
                                            $content .= "                     <agm-marker [latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_latitude * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."_longitude * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                            $content .= "                  </agm-map>\n";
                                        } else {
                                            $content .= "                  <agm-map class=\"col-12\" style=\"height: 200px;\"[latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[1] * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[0] * 1\" [zoom]=\"15\" (mapClick)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\">\n";
                                            $content .= "                     <agm-marker [latitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[1] * 1\" [longitude]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name']).".coordinates[0] * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".UtilitiesController::checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                            $content .= "                  </agm-map>\n";
                                        }
                                    } else {
                                        $content .= "                  <input type=\"text\" class=\"form-control\" id=\"".UtilitiesController::checkNames($column['name'])."\" name=\"".UtilitiesController::checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($column['name'])."\">\n";
                                    }
                                }
                            }
                        }
                    }
                }
                $content .= "               </div>\n";
                $content .= "            </div>\n";
            }
        }
        $relationships = $args['RelationShip'];
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "            <div class=\"form-group row\">\n";
                    $content .= "               <label for=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" class=\"col-4 col-form-label\">".$relationship['fromSingular']."</label>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <select class=\"form-control\" id=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromSingular'])."_id\">\n";
                    $content .= "                     <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                     <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($relationship['fromPlural'])."\" value={{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                        {{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                     </option>\n";
                    $content .= "                  </select>\n";
                    $content .= "               </div>\n";
                    $content .= "            </div>\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "            <div class=\"form-group row\">\n";
                    $content .= "               <label class=\"col-12 col-form-label mb-2\"><strong>".$relationship['fromPlural']."</strong></label>\n";
                    $content .= "               <label class=\"col-4 col-form-label\">".$relationship['fromSingular']."</label>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <div class=\"input-group\">\n";
                    $content .= "                     <div class=\"input-group-prepend\">\n";
                    $content .= "                        <button type=\"button\" title=\"Eliminar\" class=\"btn btn-danger\" (click)=\"remove".$relationship['fromSingular']."()\"><i class=\"fas fa-trash\"></i></button>\n";
                    $content .= "                        <button type=\"button\" title=\"Agregar\" class=\"btn btn-success\" (click)=\"add".$relationship['fromSingular']."()\"><i class=\"fas fa-plus-circle\"></i></button>\n";
                    $content .= "                     </div>\n";
                    $content .= "                     <select class=\"form-control\" id=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId\">\n";
                    $content .= "                        <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                        <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($relationship['fromPlural'])."\" value={{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                           {{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                        </option>\n";
                    $content .= "                     </select>\n";
                    $content .= "                  </div>\n";
                    $content .= "               </div>\n";
                    $content .= "               <div class=\"col-4\">\n";
                    $content .= "               </div>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <table class=\"table table-hover mt-2\">\n";
                    $content .= "                     <tbody>\n";
                    $content .= "                        <tr *ngFor=\"let ".UtilitiesController::checkNames($relationship['fromSingular'])." of ".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."\" (click)=\"select".$relationship['fromSingular']."(".UtilitiesController::checkNames($relationship['fromSingular']).")\">\n";
                    $content .= "                           <td class=\"text-right\"><span *ngIf=\"".UtilitiesController::checkNames($relationship['fromPlural'])."_".UtilitiesController::checkNames($relationship['toSingular'])."SelectedId === ".UtilitiesController::checkNames($relationship['fromSingular']).".id\" class=\"far fa-hand-point-right\"></span></td>\n";
                    $content .= "                           <td>{{".UtilitiesController::checkNames($relationship['fromSingular']).".id}}</td>\n";
                    $content .= "                        </tr>\n";
                    $content .= "                     </tbody>\n";
                    $content .= "                  </table>\n";
                    $content .= "               </div>\n";
                    $content .= "            </div>\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "            <div class=\"form-group row\">\n";
                    $content .= "               <label for=\"".UtilitiesController::checkNames($relationship['fromSingular'])."_id\" class=\"col-4 col-form-label\">".$relationship['fromSingular']."</label>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <select class=\"form-control\" id=\"".UtilitiesController::checkNames($relationship['toSingular'])."_id\" name=\"".UtilitiesController::checkNames($relationship['toSingular'])."_id\" [(ngModel)]=\"".UtilitiesController::checkNames($tableNameSingular)."Selected.".UtilitiesController::checkNames($relationship['toSingular'])."_id\">\n";
                    $content .= "                     <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                     <option *ngFor=\"let ".UtilitiesController::checkNames($relationship['toSingular'])." of ".UtilitiesController::checkNames($relationship['toPlural'])."\" value={{".UtilitiesController::checkNames($relationship['toSingular']).".id}}>\n";
                    $content .= "                        {{".UtilitiesController::checkNames($relationship['toSingular']).".id}}\n";
                    $content .= "                     </option>\n";
                    $content .= "                  </select>\n";
                    $content .= "               </div>\n";
                    $content .= "            </div>\n";
                }
            }
        }
        $content .= "         </div>\n";
        $content .= "      </div>\n";
        $content .= "   </div>\n";
        $content .= "   <div class=\"modal-footer\">\n";
        $content .= "      <button type=\"button\" class=\"btn btn-outline-success\" (click)=\"modal.close('Guardar click')\">Guardar</button>\n";
        $content .= "      <button type=\"button\" class=\"btn btn-outline-danger\" (click)=\"modal.close('Cancelar click')\">Cancelar</button>\n";
        $content .= "   </div>\n";
        $content .= "</ng-template>";
        return $content;
    }
}
