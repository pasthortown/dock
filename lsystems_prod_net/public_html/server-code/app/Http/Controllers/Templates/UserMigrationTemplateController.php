<?php

namespace App\Http\Controllers;

class UserMigrationTemplateController extends Controller
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
        $bddType = $args['bddType'];
        $content = "<?php\n";
        $content .= "\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        }
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "\n";
        $content .= "class CreateUsersTable extends Migration\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n";
        $content .= "    {\n";
        if ($bddType == "SQL") {
            $content .= "       Schema::create('users', function (Blueprint \$table) {\n";
            $content .= "          \$table->increments('id');\n";
            $content .= "          \$table->timestamps();\n";
            $content .= "          \$table->string('name',100)->nullable(\$value = true);\n";
            $content .= "          \$table->string('email',255)->nullable(\$value = true);\n";
            $content .= "          \$table->string('password')->nullable(\$value = true);\n";
            $content .= "          \$table->string('api_token',255)->nullable(\$value = true);\n";
            $content .= "       });\n";
        } else {
            $content .= "       Schema::create('users', function (\$collection) {\n";
            $content .= "           \$collection->unique('email');\n";
            $content .= "       });\n";
        }
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function down()\n";
        $content .= "    {\n";
        $content .= "       Schema::dropIfExists('users');\n";
        $content .= "    }\n";
        $content .= "}\n";
        return $content;
    }
}
