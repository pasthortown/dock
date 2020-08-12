<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class ModelTSTemplateController extends Controller
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

    static function build($args, $bddType) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $content = "";
        foreach ($relationships as $relationship) {
            if ($bddType == "SQL") {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "import { ".$relationship['fromSingular']." } from './".$relationship['fromSingular']."';\n";
                    }
                }
            } else {
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['toSingular'] === 'User') {
                        $content .= "import { User } from './../profile/User';\n";
                    } else {
                        $content .= "import { ".$relationship['toSingular']." } from './".$relationship['toSingular']."';\n";
                    }
                }
            }
        }
        $content .= "\nexport class ".$tableNameSingular." {\n";
        $content .= "   id: number;\n";
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                $insertado = false;
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "   ".UtilitiesController::checkNames($column['name']).": Date;\n";
                    $insertado = true;
                }
                if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                    $content .= "   ".UtilitiesController::checkNames($column['name']).": number;\n";
                    $insertado = true;
                }
                if ($column['type']==='text' || $column['type']==='string' || $column['type']==='mediumText' || $column['type']==='longText' || $column['type']==='lineString' || $column['type']==='char') {
                    $content .= "   ".UtilitiesController::checkNames($column['name']).": String;\n";
                    $insertado = true;
                }
                if ($column['type']==='boolean') {
                    $content .= "   ".UtilitiesController::checkNames($column['name']).": Boolean;\n";
                    $insertado = true;
                }
                if ($column['type']==='gmap') {
                    if ($bddType == "SQL") {
                        $content .= "   ".UtilitiesController::checkNames($column['name'])."_latitude: number;\n";
                        $content .= "   ".UtilitiesController::checkNames($column['name'])."_longitude: number;\n";
                    } else {
                        $content .= "   ".UtilitiesController::checkNames($column['name']).": any;\n";
                    }
                    $insertado = true;
                }
                if (!$insertado){
                    $content .= "   ".UtilitiesController::checkNames($column['name']).": any;\n";
                }
            }
        }
        $needConstructor = false;
        foreach ($tableColumns as $column) {
            if ($column['type']==='gmap') {
                $needConstructor = true;
            }
        }
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "   ".UtilitiesController::checkNames($relationship['fromSingular'])."_id: number;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $needConstructor = true;
                    $content .= "   ".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular']).": ".$relationship['fromSingular']."[];\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   ".UtilitiesController::checkNames($relationship['toSingular'])."_id: number;\n";
                }
                if ($bddType !== "SQL") {
                    $needConstructor = true;
                    if ($relationship['kind'] === 'embedsone') {
                        $content .= "   ".UtilitiesController::checkNames($relationship['toSingular']).": ". $relationship['toSingular'] .";\n";
                    } else {
                        $content .= "   ".UtilitiesController::checkNames($relationship['toSingular']).": ". $relationship['toSingular'] ."[];\n";
                    }
                }
            }
        }
        if ($needConstructor) {
            $content .= "   constructor() {\n";
            foreach ($relationships as $relationship) {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "      this.".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = [];\n";
                    }
                }
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($bddType !== "SQL") {
                        if ($relationship['kind'] === 'embedsone') {
                            $content .= "      this.".UtilitiesController::checkNames($relationship['toSingular'])." = new ". $relationship['toSingular'] ."();\n";
                        } else {
                            $content .= "      this.".UtilitiesController::checkNames($relationship['toSingular'])." = [];\n";
                        }
                    }
                }
            }
            foreach ($tableColumns as $column) {
                if ($column['type']==='gmap') {
                    if ($bddType == "SQL") {
                        $content .= "      this.".UtilitiesController::checkNames($column['name'])."_latitude = 0;\n";
                        $content .= "      this.".UtilitiesController::checkNames($column['name'])."_longitude = 0;\n";
                    } else {
                        $content .= "   ".UtilitiesController::checkNames($column['name'])." = { type: \"Point\", coordinates: [ 0, 0 ] };\n";
                    }
                }
            }
            $content .= "   }\n";
        }
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }
}
