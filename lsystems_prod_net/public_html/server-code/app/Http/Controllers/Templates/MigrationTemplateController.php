<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class MigrationTemplateController extends Controller
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
        $migrationIndex = $args['MigrationIndex'];
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $columnsFinal = [];
        foreach ($tableColumns as $column) {
            $existe = false;
            foreach ($columnsFinal as $c2) {
                if ($column['name'] === $c2['name']) {
                    $existe = true;
                }
            }
            if(!$existe) {
                array_push($columnsFinal, $column);
            }
        }
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        }
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n\n";
        $content .= "class Create".$tableNamePlural."Table extends Migration\n{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n    {\n";
        if ($bddType == "SQL") {
            $content .= "       Schema::create('".UtilitiesController::checkNames($tableNamePlural)."', function (Blueprint \$table) {\n";
            $content .= "          \$table->increments('id');\n";
            $content .= "          \$table->timestamps();\n";
            $unicidad = "          \$table->unique([";
            $hasUniques = false;
            foreach ($columnsFinal as $column) {
                if($column['type']==="gmap") {
                    if($column['unique']) {
                        $unicidad .= "'".UtilitiesController::checkNames($column['name'])."_latitude',";
                        $unicidad .= "'".UtilitiesController::checkNames($column['name'])."_longitude',";
                    }
                    $content .= "          \$table->float('".UtilitiesController::checkNames($column['name'])."_latitude',24,16)->nullable(\$value = true);\n";
                    $content .= "          \$table->float('".UtilitiesController::checkNames($column['name'])."_longitude',24,16)->nullable(\$value = true);\n";
                } else {
                    $size = $column['size'];
                    $hasSize = $column['hasSize'];
                    $content .= "          \$table->".$column['type']."('".UtilitiesController::checkNames($column['name'])."'";
                    if($hasSize) {
                        $content .= ",".$column['size'];
                    }
                    if($column['type']==='double') {
                        $content .= ",8,2";
                    }
                    if($column['canNull']) {
                        $content .= ")->nullable(\$value = true);\n";
                    }else {
                        $content .= ")->nullable(\$value = false);\n";
                    }
                    if($column['unique'] && !$hasUniques) {
                        $hasUniques = true;
                    }
                    if($column['unique']) {
                        $unicidad .= "'".UtilitiesController::checkNames($column['name'])."',";
                    }
                }
            }
            foreach ($relationships as $relationship) {
                if ($relationship['kind'] !== 'many2many') {
                    if ($relationship['toSingular'] === $tableNameSingular) {
                        $relCode = "          \$table->unsignedInteger('". UtilitiesController::checkNames($relationship['fromSingular']) ."_id');\n";
                        $relCode .= "          \$table->foreign('". UtilitiesController::checkNames($relationship['fromSingular']) ."_id')->references('id')->on('". UtilitiesController::checkNames($relationship['fromPlural']) ."')->onDelete('cascade');\n";
                        $content .= $relCode;
                    }
                }
            }
            $unicidad = trim($unicidad, ",");
            $unicidad .= "]);\n";
            if($hasUniques) {
                $content .= $unicidad;
            }
            $content .= "       });\n";
        } else {
            $content .= "       Schema::create('".UtilitiesController::checkNames($tableNamePlural)."', function (\$collection) {\n";
            foreach ($columnsFinal as $column) {
                if($column['type']==="gmap") {
                    $content .= "          \$collection->geospatial('".UtilitiesController::checkNames($column['name'])."', '2d');\n";
                }
                if($column['unique']) {
                    $content .= "          \$collection->unique('".UtilitiesController::checkNames($column['name'])."');\n";
                }
            }
            $content .= "       });\n";
        }
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function down()\n    {\n";
        $content .= "       Schema::dropIfExists('".UtilitiesController::checkNames($tableNamePlural)."');\n    }\n}";
        return ["Table"=>$args['Table'], "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }
}
