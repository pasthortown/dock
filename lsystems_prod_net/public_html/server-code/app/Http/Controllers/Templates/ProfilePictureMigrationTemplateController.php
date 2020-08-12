<?php

namespace App\Http\Controllers;

class ProfilePictureMigrationTemplateController extends Controller
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
        $content .= "class CreateProfilePicturesTable extends Migration\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n";
        $content .= "    {\n";
        if ($bddType == "SQL") {
            $content .= "       Schema::create('profile_pictures', function (Blueprint \$table) {\n";
            $content .= "          \$table->increments('id');\n";
            $content .= "          \$table->timestamps();\n";
            $content .= "          \$table->string('file_type',50)->nullable(\$value = true);\n";
            $content .= "          \$table->string('file_name',50)->nullable(\$value = true);\n";
            $content .= "          \$table->longText('file')->nullable(\$value = true);\n";
            $content .= "          \$table->unsignedInteger('id_user');\n";
            $content .= "          \$table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');\n";
            $content .= "       });\n";
        } else {
            $content .= "       Schema::create('profile_pictures', function (\$collection) {\n";
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
        $content .= "       Schema::dropIfExists('profile_pictures');\n";
        $content .= "    }\n";
        $content .= "}\n";
        return $content;
    }
}
