<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class RelationshipMany2ManyTemplateController extends Controller
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
        $toSingular = $args['toSingular'];
        $toPlural = $args['toPlural'];
        $fromSingular = $args['fromSingular'];
        $fromPlural = $args['fromPlural'];
        $migrationIndex = 999;
        $migrationName = $toSingular.$fromSingular;
        $tableName = UtilitiesController::checkNames($toSingular)."_".UtilitiesController::checkNames($fromSingular);
        $table = ["nameSingular"=>$toSingular.$fromSingular,"namePlural"=>$toPlural.$fromPlural];
        if (strcasecmp($toSingular, $fromSingular) > 0) {
            $migrationName = $fromSingular.$toSingular;
            $tableName = UtilitiesController::checkNames($fromSingular)."_".UtilitiesController::checkNames($toSingular);
            $table = ["nameSingular"=>$fromSingular.$toSingular,"namePlural"=>$fromSingular.$toSingular];
        }
        $content = "<?php\n\n";
        $content .= "use Illuminate\Support\Facades\Schema;\n";
        $content .= "use Illuminate\Database\Schema\Blueprint;\n";
        $content .= "use Illuminate\Database\Migrations\Migration;\n\n";
        $content .= "class Create".$migrationName."Table extends Migration\n{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n    {\n";
        $content .= "       Schema::create('".$tableName."', function (Blueprint \$table) {\n";
        $content .= "          \$table->increments('id');\n";
        $content .= "          \$table->timestamps();\n";
        $content .= "          \$table->unsignedInteger('". UtilitiesController::checkNames($fromSingular) ."_id');\n";
        $content .= "          \$table->foreign('". UtilitiesController::checkNames($fromSingular) ."_id')->references('id')->on('". UtilitiesController::checkNames($fromPlural) ."')->onDelete('cascade');\n";
        $content .= "          \$table->unsignedInteger('". UtilitiesController::checkNames($toSingular) ."_id');\n";
        $content .= "          \$table->foreign('". UtilitiesController::checkNames($toSingular) ."_id')->references('id')->on('". UtilitiesController::checkNames($toPlural) ."')->onDelete('cascade');\n";
        $content .= "       });\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function down()\n    {\n";
        $content .= "       Schema::dropIfExists('".$tableName."');\n    }\n}";
        return ["Table"=>$table, "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }
}
