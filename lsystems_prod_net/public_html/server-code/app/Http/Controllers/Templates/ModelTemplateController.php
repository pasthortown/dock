<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class ModelTemplateController extends Controller
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
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $content = "<?php\n\n";
        $content .= "namespace App;\n\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
        } else {
            $content .= "use Jenssegers\\Mongodb\\Eloquent\\Model;\n\n";
        }
        $content .= "class ".$tableNameSingular." extends Model\n{\n";
        if ($bddType !== "SQL") {
            $content .= "    protected \$collection = '".UtilitiesController::checkNames($tableNamePlural)."';\n";
            $content .= "    protected \$primaryKey = 'id';\n\n";
        }
        $content .= "    /**\n";
        $content .= "     * The attributes that are mass assignable.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$fillable = [\n";
        if ($bddType == "SQL") {
            $content .= "       ";
        } else {
            $content .= "       'id',";
        }
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                if($column['type']==="gmap") {
                    if ($bddType == "SQL") {
                        $content .= "'".UtilitiesController::checkNames($column['name'])."_latitude',";
                        $content .= "'".UtilitiesController::checkNames($column['name'])."_longitude',";
                    } else {
                        $content .= "'".UtilitiesController::checkNames($column['name'])."',";
                    }
                } else {
                    $content .= "'".UtilitiesController::checkNames($column['name'])."',";
                }
            }
        }
        $content .= "\n    ];\n\n";
        $content .= "    /**\n";
        $content .= "     * The attributes excluded from the model's JSON form.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$hidden = [\n";
        $content .= "       ";
        foreach ($tableColumns as $column) {
            if($column['group']==="hidden") {
                if($column['type']==="gmap") {
                    if ($bddType == "SQL") {
                        $content .= "'".UtilitiesController::checkNames($column['name'])."_latitude',";
                        $content .= "'".UtilitiesController::checkNames($column['name'])."_longitude',";
                    } else {
                        $content .= "'".UtilitiesController::checkNames($column['name'])."',";
                    }
                } else {
                    $content .= "'".UtilitiesController::checkNames($column['name'])."',";
                }
            }
        }
        $content .= "\n    ];\n\n";
        if ($bddType == "SQL") {
            foreach ($relationships as $relationship) {
                $withTimeStamps = "";
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'one2one') {
                        $relKind = 'belongsTo';
                        $functionName = UtilitiesController::checkNames($relationship['toSingular']);
                        $parameterName = $relationship['toSingular'];
                    }
                    if ($relationship['kind'] === 'one2many') {
                        $relKind = 'hasMany';
                        $functionName = UtilitiesController::checkNames($relationship['toPlural']);
                        $parameterName = $relationship['toSingular'];
                    }
                    if ($relationship['kind'] === 'many2one') {
                        $relKind = 'belongsTo';
                        $functionName = UtilitiesController::checkNames($relationship['toSingular']);
                        $parameterName = $relationship['toSingular'];
                    }
                    if ($relationship['kind'] === 'many2many') {
                        $relKind = 'belongsToMany';
                        $functionName = UtilitiesController::checkNames($relationship['toPlural']);
                        $parameterName = $relationship['toSingular'];
                        $withTimeStamps = "->withTimestamps()";
                    }
                }
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'one2one') {
                        $relKind = 'hasOne';
                        $functionName = UtilitiesController::checkNames($relationship['fromSingular']);
                        $parameterName = $relationship['fromSingular'];
                    }
                    if ($relationship['kind'] === 'one2many') {
                        $relKind = 'belongsTo';
                        $functionName = UtilitiesController::checkNames($relationship['fromSingular']);
                        $parameterName = $relationship['fromSingular'];
                    }
                    if ($relationship['kind'] === 'many2one') {
                        $relKind = 'hasMany';
                        $functionName = UtilitiesController::checkNames($relationship['fromPlural']);
                        $parameterName = $relationship['fromSingular'];
                    }
                    if ($relationship['kind'] === 'many2many') {
                        $relKind = 'belongsToMany';
                        $functionName = UtilitiesController::checkNames($relationship['fromPlural']);
                        $parameterName = $relationship['fromSingular'];
                        $withTimeStamps = "->withTimestamps()";
                    }
                }
                $relCode = "    function ". $functionName ."()\n";
                $relCode .= "    {\n";
                $relCode .= "       return \$this->". $relKind ."('App\\". $parameterName ."')". $withTimeStamps .";\n";
                $relCode .= "    }\n\n";
                $content .= $relCode;
            }
        } else {
            foreach ($relationships as $relationship) {
                if ($relationship['fromSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'embedsmany') {
                        $relKind = 'embedsMany';
                        $functionName = UtilitiesController::checkNames($relationship['toSingular']);
                        $parameterName = $relationship['toSingular'];
                    }
                    if ($relationship['kind'] === 'embedsone') {
                        $relKind = 'embedsOne';
                        $functionName = UtilitiesController::checkNames($relationship['toSingular']);
                        $parameterName = $relationship['toSingular'];
                    }
                    $relCode = "    function ". $functionName ."()\n";
                    $relCode .= "    {\n";
                    $relCode .= "       return \$this->". $relKind ."('App\\". $parameterName ."')" .";\n";
                    $relCode .= "    }\n\n";
                    $content .= $relCode;
                }
            }
        }
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }
}
