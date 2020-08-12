<?php

class ApiGenerator {
    private $Models_dir;

    public function __construct($Models_dir) {
        $this->Models_dir=$Models_dir;
    }

    public function getFiles() {
        $rootPath = realpath($this->Models_dir);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        $toReturn = [];
        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                array_push($toReturn, $filePath);
            }
        }
        return $toReturn;
    }

    public function readMyFile($file_path) {
        $tableName = basename($file_path, ".php");
        $file = fopen($file_path, "r");
        $content = fread($file,filesize($file_path));
        fclose($file);
        return ["Table"=>$tableName, "Content"=>$content];
    }

    public function getTableData($file) {
        $content = $file['Content'];
        $tableName = $file['Table'];
        $fillables = explode(',',trim(explode('];',trim(explode('protected $fillable = [',$content)[1]))[0]));
        $columnas = [];
        foreach ($fillables as $column) {
            if($column !== "") {
                $columna = ["name"=>trim(trim($column, '\''), ' \''),"type"=>"string","size"=>255, "group"=>"fillable", "canNull"=>true, "unique"=>false];
                array_push($columnas, $columna);
            }
        }
        $hiddenSearch = explode('protected $hidden = [',$content);
        if(sizeof($hiddenSearch) > 1) {
            $hiddens = explode(',',trim(explode('];',trim($hiddenSearch[1]))[0]));
            foreach ($hiddens as $column) {
                if($column !== "") {
                    $columna = ["name"=>trim(trim($column, '\''), ' \''),"type"=>"string","size"=>255, "group"=>"hidden", "canNull"=>true, "unique"=>false];
                    array_push($columnas, $columna);
                }
            }
        }
        $table=["nameSingular"=>$tableName,"namePlural"=>$tableName.'s'];
        return ["Table"=>$table, "Columns"=>$columnas, "RelationShip"=>array()];
    }

    private function buildMigration($args) {
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
        $content .= "use Illuminate\Support\Facades\Schema;\n";
        $content .= "use Illuminate\Database\Schema\Blueprint;\n";
        $content .= "use Illuminate\Database\Migrations\Migration;\n\n";
        $content .= "class Create".$tableNamePlural."Table extends Migration\n{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n    {\n";
        $content .= "       Schema::create('".$this->checkNames($tableNamePlural)."', function (Blueprint \$table) {\n";
        $content .= "          \$table->increments('id');\n";
        $content .= "          \$table->timestamps();\n";
        $unicidad = "          \$table->unique([";
        $hasUniques = false;
        foreach ($columnsFinal as $column) {
            if($column['type']==="gmap") {
                $content .= "          \$table->float('".$this->checkNames($column['name'])."_latitude',24,16)->nullable(\$value = true);\n";
                $content .= "          \$table->float('".$this->checkNames($column['name'])."_longitude',24,16)->nullable(\$value = true);\n";
            } else {
                $size = $column['size'];
                $hasSize = $column['hasSize'];
                $content .= "          \$table->".$column['type']."('".$this->checkNames($column['name'])."'";
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
                    $unicidad .= "'".$this->checkNames($column['name'])."',";
                }
            }
        }
        foreach ($relationships as $relationship) {
            if ($relationship['kind'] !== 'many2many') {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    $relCode = "          \$table->unsignedInteger('". $this->checkNames($relationship['fromSingular']) ."_id');\n";
                    $relCode .= "          \$table->foreign('". $this->checkNames($relationship['fromSingular']) ."_id')->references('id')->on('". $this->checkNames($relationship['fromPlural']) ."')->onDelete('cascade');\n";
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
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function down()\n    {\n";
        $content .= "       Schema::dropIfExists('".$this->checkNames($tableNamePlural)."');\n    }\n}";
        return ["Table"=>$args['Table'], "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }

    private function buildController($args) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $migrationIndex = $args['MigrationIndex'];
        $colsAsText = "";
        $colsAsTextNoHidden = "";
        $colsHidden = [];
        $colsVisibles = [];
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if ($column['type']==="gmap") {
                    array_push($colsVisibles,$this->checkNames($column['name'])."_latitude");
                    array_push($colsVisibles,$this->checkNames($column['name'])."_longitude");
                } else {
                    array_push($colsVisibles,$this->checkNames($column['name']));
                }
            } else {
                if ($column['type']==="gmap") {
                    array_push($colsHidden,$this->checkNames($column['name'])."_latitude");
                    array_push($colsHidden,$this->checkNames($column['name'])."_longitude");
                } else {
                    array_push($colsHidden,$this->checkNames($column['name']));
                }
            }
        }
        foreach ($colsVisibles as $visible) {
            $isHidden = false;
            foreach ($colsHidden as $hidden) {
                if ($visible === $hidden) {
                    $isHidden = true;
                }
            }
            $colsAsText .= "          \$". strtolower($tableNameSingular) ."->".$visible." = \$result['".$visible."'];\n";
            if (!$isHidden) {
                $colsAsTextNoHidden .= "             '".$visible."'=>\$result['".$visible."'],\n";
            }
        }
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $colsAsText .= "          \$". strtolower($tableNameSingular) ."->".$this->checkNames($relationship['fromSingular'])."_id = \$result['".$this->checkNames($relationship['fromSingular'])."_id'];\n";
                    $colsAsTextNoHidden .= "             '".$this->checkNames($relationship['fromSingular'])."_id'=>\$result['".$this->checkNames($relationship['fromSingular'])."_id'],\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $colsAsText .= "          \$". strtolower($tableNameSingular) ."->".$this->checkNames($relationship['toSingular'])."_id = \$result['".$this->checkNames($relationship['toSingular'])."_id'];\n";
                }
            }
        }
        $content = "<?php\n\n";
        $content .= "namespace App\\Http\\Controllers;\n\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "Use Exception;\n";
        $content .= "use App\\". $tableNameSingular .";\n";
        $content .= "use Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n";
        $content .= "use Illuminate\\Database\\Eloquent\\ModelNotFoundException;\n\n";
        $content .= "class ". $tableNameSingular ."Controller extends Controller\n{\n";
        $content .= "    function get(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$id = \$data['id'];\n";
        $content .= "       if (\$id == null) {\n";
        $content .= "          return response()->json(". $tableNameSingular ."::get(),200);\n";
        $content .= "       } else {\n";
        $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::findOrFail(\$id);\n";
        $content .= "          \$attach = [];\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "          \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                    $content .= "          array_push(\$attach, [\"".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."\"=>\$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."]);\n";
                }
            }
        }
        $content .= "          return response()->json([\"". $tableNameSingular ."\"=>\$". strtolower($tableNameSingular) .", \"attach\"=>\$attach],200);\n";
        $content .= "       }\n";
        $content .= "    }\n\n";
        $content .= "    function paginate(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$size = \$data['size'];\n";
        $content .= "       return response()->json(". $tableNameSingular ."::paginate(\$size),200);\n";
        $content .= "    }\n\n";
        $content .= "    function post(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          DB::beginTransaction();\n";
        $content .= "          \$result = \$data->json()->all();\n";
        $content .= "          \$". strtolower($tableNameSingular) ." = new ". $tableNameSingular ."();\n";
        $content .= "          \$last". $tableNameSingular ." = ". $tableNameSingular ."::orderBy('id')->get()->last();\n";
        $content .= "          if(\$last". $tableNameSingular .") {\n";
        $content .= "             \$". strtolower($tableNameSingular) ."->id = \$last". $tableNameSingular ."->id + 1;\n";
        $content .= "          } else {\n";
        $content .= "             \$". strtolower($tableNameSingular) ."->id = 1;\n";
        $content .= "          }\n";
        $content .= $colsAsText;
        $content .= "          \$". strtolower($tableNameSingular) ."->save();\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "          \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = \$result['".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."'];\n";
                    $content .= "          foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as \$".$this->checkNames($relationship['fromSingular']).") {\n";
                    $content .= "             \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".$this->checkNames($relationship['fromSingular'])."['id']);\n";
                    $content .= "          }\n";
                }
            }
        }
        $content .= "          DB::commit();\n";
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "       return response()->json(\$". strtolower($tableNameSingular) .",200);\n";
        $content .= "    }\n\n";
        $content .= "    function put(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          DB::beginTransaction();\n";
        $content .= "          \$result = \$data->json()->all();\n";
        $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::where('id',\$result['id'])->update([\n";
        $content .= $colsAsTextNoHidden;
        $content .= "          ]);\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::where('id',\$result['id'])->first();\n";
                    $content .= "          \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = \$result['".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."'];\n";
                    $content .= "          \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                    $content .= "          foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old as \$".$this->checkNames($relationship['fromSingular'])."_old ) {\n";
                    $content .= "             \$delete = true;\n";
                    $content .= "             foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as \$".$this->checkNames($relationship['fromSingular'])." ) {\n";
                    $content .= "                if ( \$".$this->checkNames($relationship['fromSingular'])."_old->id === \$".$this->checkNames($relationship['fromSingular'])."['id'] ) {\n";
                    $content .= "                   \$delete = false;\n";
                    $content .= "                }\n";
                    $content .= "             }\n";
                    $content .= "             if ( \$delete ) {\n";
                    $content .= "                \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->detach(\$".$this->checkNames($relationship['fromSingular'])."_old->id);\n";
                    $content .= "             }\n";
                    $content .= "          }\n";
                    $content .= "          foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as \$".$this->checkNames($relationship['fromSingular'])." ) {\n";
                    $content .= "             \$add = true;\n";
                    $content .= "             foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old as \$".$this->checkNames($relationship['fromSingular'])."_old) {\n";
                    $content .= "                if ( \$".$this->checkNames($relationship['fromSingular'])."_old->id === \$".$this->checkNames($relationship['fromSingular'])."['id'] ) {\n";
                    $content .= "                   \$add = false;\n";
                    $content .= "                }\n";
                    $content .= "             }\n";
                    $content .= "             if ( \$add ) {\n";
                    $content .= "                \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".$this->checkNames($relationship['fromSingular'])."['id']);\n";
                    $content .= "             }\n";
                    $content .= "          }\n";
                }
            }
        }
        $content .= "          DB::commit();\n";
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "       return response()->json(\$". strtolower($tableNameSingular) .",200);\n";
        $content .= "    }\n\n";
        $content .= "    function delete(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$id = \$data['id'];\n";
        $content .= "       return ". $tableNameSingular ."::destroy(\$id);\n";
        $content .= "    }\n\n";
        $content .= "    function backup(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$". strtolower($tableNamePlural) ." = ". $tableNameSingular ."::get();\n";
        $content .= "       \$toReturn = [];\n";
        $content .= "       foreach( \$". strtolower($tableNamePlural) ." as \$". strtolower($tableNameSingular) .") {\n";
        $content .= "          \$attach = [];\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "          \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                    $content .= "          array_push(\$attach, [\"".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."\"=>\$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."]);\n";
                }
            }
        }
        $content .= "          array_push(\$toReturn, [\"". $tableNameSingular ."\"=>\$". strtolower($tableNameSingular) .", \"attach\"=>\$attach]);\n";
        $content .= "       }\n";
        $content .= "       return response()->json(\$toReturn,200);\n";
        $content .= "    }\n\n";
        $content .= "    function masiveLoad(Request \$data)\n";
        $content .= "    {\n";
        $content .= "      \$incomming = \$data->json()->all();\n";
        $content .= "      \$masiveData = \$incomming['data'];\n";
        $content .= "      try{\n";
        $content .= "       DB::beginTransaction();\n";
        $content .= "       foreach(\$masiveData as \$row) {\n";
        $content .= "         \$result = \$row['". $tableNameSingular ."'];\n";
        $content .= "         \$exist = ". $tableNameSingular ."::where('id',\$result['id'])->first();\n";
        $content .= "         if (\$exist) {\n";
        $content .= "           ". $tableNameSingular ."::where('id', \$result['id'])->update([\n";
        $content .= $colsAsTextNoHidden;
        $content .= "           ]);\n";
        $content .= "         } else {\n";
        $content .= "          \$". strtolower($tableNameSingular) ." = new ". $tableNameSingular ."();\n";
        $content .= "          \$". strtolower($tableNameSingular) ."->id = \$result['id'];\n";
        $content .= $colsAsText;
        $content .= "          \$". strtolower($tableNameSingular) ."->save();\n";
        $content .= "         }\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "         \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::where('id',\$result['id'])->first();\n";
                    $content .= "         \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = [];\n";
                    $content .= "         foreach(\$row['attach'] as \$attach){\n";
                    $content .= "            \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = \$attach['".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."'];\n";
                    $content .= "         }\n";
                    $content .= "         \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                    $content .= "         foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old as \$".$this->checkNames($relationship['fromSingular'])."_old ) {\n";
                    $content .= "            \$delete = true;\n";
                    $content .= "            foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as \$".$this->checkNames($relationship['fromSingular'])." ) {\n";
                    $content .= "               if ( \$".$this->checkNames($relationship['fromSingular'])."_old->id === \$".$this->checkNames($relationship['fromSingular'])."['id'] ) {\n";
                    $content .= "                  \$delete = false;\n";
                    $content .= "               }\n";
                    $content .= "            }\n";
                    $content .= "            if ( \$delete ) {\n";
                    $content .= "               \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->detach(\$".$this->checkNames($relationship['fromSingular'])."_old->id);\n";
                    $content .= "            }\n";
                    $content .= "         }\n";
                    $content .= "         foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as \$".$this->checkNames($relationship['fromSingular'])." ) {\n";
                    $content .= "            \$add = true;\n";
                    $content .= "            foreach( \$".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."_old as \$".$this->checkNames($relationship['fromSingular'])."_old) {\n";
                    $content .= "               if ( \$".$this->checkNames($relationship['fromSingular'])."_old->id === \$".$this->checkNames($relationship['fromSingular'])."['id'] ) {\n";
                    $content .= "                  \$add = false;\n";
                    $content .= "               }\n";
                    $content .= "            }\n";
                    $content .= "            if ( \$add ) {\n";
                    $content .= "               \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".$this->checkNames($relationship['fromSingular'])."['id']);\n";
                    $content .= "            }\n";
                    $content .= "         }\n";
                }
            }
        }
        $content .= "       }\n";
        $content .= "       DB::commit();\n";
        $content .= "      } catch (Exception \$e) {\n";
        $content .= "         return response()->json(\$e,400);\n";
        $content .= "      }\n";
        $content .= "      return response()->json('Task Complete',200);\n";
        $content .= "    }\n";
        $content .= "}";
        return ["Table"=>$args['Table'], "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }

    private function buildRouterFile($args) {
        $models = $args['models'];
        $moduleName = $args['moduleName'];
        $content = "<?php\n\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Application Routes\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Here is where you can register all of the routes for an application.\n";
        $content .= "| It is a breeze. Simply tell Lumen the URIs it should respond to\n";
        $content .= "| and give it the Closure to call when that URI is requested.\n";
        $content .= "|\n";
        $content .= "*/\n\n";
        $content .= "\$router->get('/', function () use (\$router) {\n";
        $content .= "   return 'Web Wervice Realizado con LSCodeGenerator';\n";
        $content .= "});\n\n";
        $content .= "\$router->group(['middleware' => []], function () use (\$router) {\n";
        $content .= "   \$router->post('/login', ['uses' => 'AuthController@login']);\n";
        $content .= "   \$router->post('/register', ['uses' => 'AuthController@register']);\n";
        $content .= "   \$router->post('/password_recovery_request', ['uses' => 'AuthController@passwordRecoveryRequest']);\n";
        $content .= "   \$router->get('/password_recovery', ['uses' => 'AuthController@passwordRecovery']);\n";
        $content .= "});\n\n";
        $content .= "\$router->group(['middleware' => ['auth']], function () use (\$router) {\n";
        $content .= "   \$router->post('/user/password_change', ['uses' => 'AuthController@passwordChange']);\n\n";
        $content .= "\n   //".$moduleName."\n\n";
        $content .= "   //CRUD ProfilePicture\n";
        $content .= "   \$router->post('/profilepicture', ['uses' => 'ProfilePictureController@post']);\n";
        $content .= "   \$router->get('/profilepicture', ['uses' => 'ProfilePictureController@get']);\n";
        $content .= "   \$router->get('/profilepicture/paginate', ['uses' => 'ProfilePictureController@paginate']);\n";
        $content .= "   \$router->put('/profilepicture', ['uses' => 'ProfilePictureController@put']);\n";
        $content .= "   \$router->delete('/profilepicture', ['uses' => 'ProfilePictureController@delete']);\n\n";
        $content .= "   //CRUD User\n";
        $content .= "   \$router->post('/user', ['uses' => 'UserController@post']);\n";
        $content .= "   \$router->get('/user', ['uses' => 'UserController@get']);\n";
        $content .= "   \$router->get('/user/paginate', ['uses' => 'UserController@paginate']);\n";
        $content .= "   \$router->put('/user', ['uses' => 'UserController@put']);\n";
        $content .= "   \$router->delete('/user', ['uses' => 'UserController@delete']);\n";
        foreach($models as $modelo) {
            $model = $modelo['Table']['nameSingular'];
            $content .= "\n   //CRUD ".$model."\n";
            $content .= "   \$router->post('/".strtolower($model)."', ['uses' => '".$model."Controller@post']);\n";
            $content .= "   \$router->get('/".strtolower($model)."', ['uses' => '".$model."Controller@get']);\n";
            $content .= "   \$router->get('/".strtolower($model)."/paginate', ['uses' => '".$model."Controller@paginate']);\n";
            $content .= "   \$router->get('/".strtolower($model)."/backup', ['uses' => '".$model."Controller@backup']);\n";
            $content .= "   \$router->put('/".strtolower($model)."', ['uses' => '".$model."Controller@put']);\n";
            $content .= "   \$router->delete('/".strtolower($model)."', ['uses' => '".$model."Controller@delete']);\n";
            $content .= "   \$router->post('/".strtolower($model)."/masive_load', ['uses' => '".$model."Controller@masiveLoad']);\n";
        }
        $content .= "});\n";
        return ["Content"=>$content];
    }

    private function buildMigrationMany2Many($args) {
        $toSingular = $args['toSingular'];
        $toPlural = $args['toPlural'];
        $fromSingular = $args['fromSingular'];
        $fromPlural = $args['fromPlural'];
        $migrationIndex = 999;
        $content = "<?php\n\n";
        $content .= "use Illuminate\Support\Facades\Schema;\n";
        $content .= "use Illuminate\Database\Schema\Blueprint;\n";
        $content .= "use Illuminate\Database\Migrations\Migration;\n\n";
        $content .= "class Create".$toSingular.$fromSingular."Table extends Migration\n{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function up()\n    {\n";
        $content .= "       Schema::create('".$this->checkNames($toSingular)."_".$this->checkNames($fromSingular)."', function (Blueprint \$table) {\n";
        $content .= "          \$table->increments('id');\n";
        $content .= "          \$table->timestamps();\n";
        $content .= "          \$table->unsignedInteger('". $this->checkNames($fromSingular) ."_id');\n";
        $content .= "          \$table->foreign('". $this->checkNames($fromSingular) ."_id')->references('id')->on('". $this->checkNames($fromPlural) ."')->onDelete('cascade');\n";
        $content .= "          \$table->unsignedInteger('". $this->checkNames($toSingular) ."_id');\n";
        $content .= "          \$table->foreign('". $this->checkNames($toSingular) ."_id')->references('id')->on('". $this->checkNames($toPlural) ."')->onDelete('cascade');\n";
        $content .= "       });\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     *\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= "    public function down()\n    {\n";
        $content .= "       Schema::dropIfExists('".$this->checkNames($toSingular)."_".$this->checkNames($fromSingular)."');\n    }\n}";
        $table = ["nameSingular"=>$toSingular.$fromSingular,"namePlural"=>$toSingular.$fromSingular];
        return ["Table"=>$table, "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }

    private function buildModel($args) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $content = "<?php\n\n";
        $content .= "namespace App;\n\n";
        $content .= "use Illuminate\Database\Eloquent\Model;\n\n";
        $content .= "class ".$tableNameSingular." extends Model\n{\n";
        $content .= "    /**\n";
        $content .= "     * The attributes that are mass assignable.\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$fillable = [\n";
        $content .= "       ";
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                if($column['type']==="gmap") {
                    $content .= "'".$this->checkNames($column['name'])."_latitude',";
                    $content .= "'".$this->checkNames($column['name'])."_longitude',";
                } else {
                    $content .= "'".$this->checkNames($column['name'])."',";
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
                $content .= "'".$this->checkNames($column['name'])."',";
            }
        }
        $content .= "\n    ];\n\n";
        foreach ($relationships as $relationship) {
            $withTimeStamps = "";
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one') {
                    $relKind = 'belongsTo';
                    $functionName = $relationship['toSingular'];
                    $parameterName = $relationship['toSingular'];
                }
                if ($relationship['kind'] === 'one2many') {
                    $relKind = 'hasMany';
                    $functionName = $relationship['toPlural'];
                    $parameterName = $relationship['toSingular'];
                }
                if ($relationship['kind'] === 'many2one') {
                    $relKind = 'belongsTo';
                    $functionName = $relationship['toSingular'];
                    $parameterName = $relationship['toSingular'];
                }
                if ($relationship['kind'] === 'many2many') {
                    $relKind = 'belongsToMany';
                    $functionName = $relationship['toPlural'];
                    $parameterName = $relationship['toSingular'];
                    $withTimeStamps = "->withTimestamps()";
                }
            }
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one') {
                    $relKind = 'hasOne';
                    $functionName = $relationship['fromSingular'];
                    $parameterName = $relationship['fromSingular'];
                }
                if ($relationship['kind'] === 'one2many') {
                    $relKind = 'belongsTo';
                    $functionName = $relationship['fromSingular'];
                    $parameterName = $relationship['fromSingular'];
                }
                if ($relationship['kind'] === 'many2one') {
                    $relKind = 'hasMany';
                    $functionName = $relationship['fromPlural'];
                    $parameterName = $relationship['fromSingular'];
                }
                if ($relationship['kind'] === 'many2many') {
                    $relKind = 'belongsToMany';
                    $functionName = $relationship['fromPlural'];
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
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }

    private function buildModelTS($args) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $content = "";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "import { ".$relationship['fromSingular']." } from './".$relationship['fromSingular']."';\n\n";
                }
            }
        }
        $content .= "export class ".$tableNameSingular." {\n";
        $content .= "   id: number;\n";
        foreach ($tableColumns as $column) {
            if($column['group']==="fillable") {
                $insertado = false;
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "   ".$this->checkNames($column['name']).": Date;\n";
                    $insertado = true;
                }
                if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                    $content .= "   ".$this->checkNames($column['name']).": number;\n";
                    $insertado = true;
                }
                if ($column['type']==='text' || $column['type']==='string' || $column['type']==='mediumText' || $column['type']==='longText' || $column['type']==='lineString' || $column['type']==='char') {
                    $content .= "   ".$this->checkNames($column['name']).": String;\n";
                    $insertado = true;
                }
                if ($column['type']==='boolean') {
                    $content .= "   ".$this->checkNames($column['name']).": Boolean;\n";
                    $insertado = true;
                }
                if ($column['type']==='gmap') {
                    $content .= "   ".$this->checkNames($column['name'])."_latitude: number;\n";
                    $content .= "   ".$this->checkNames($column['name'])."_longitude: number;\n";
                    $insertado = true;
                }
                if (!$insertado){
                    $content .= "   ".$this->checkNames($column['name']).": any;\n";
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
                    $content .= "   ".$this->checkNames($relationship['fromSingular'])."_id: number;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $needConstructor = true;
                    $content .= "   ".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular']).": ".$relationship['fromSingular']."[];\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   ".$this->checkNames($relationship['toSingular'])."_id: number;\n";
                }
            }
        }
        if ($needConstructor) {
            $content .= "   constructor() {\n";
            foreach ($relationships as $relationship) {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = [];\n";
                    }
                }
            }
            foreach ($tableColumns as $column) {
                if ($column['type']==='gmap') {
                    $content .= "      this.".$this->checkNames($column['name'])."_latitude = 0;\n";
                    $content .= "      this.".$this->checkNames($column['name'])."_longitude = 0;\n";
                }
            }
            $content .= "   }\n";
        }
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }

    private function buildServiceTS($args, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableColumns = $args['Columns'];
        $content = "import { Injectable } from '@angular/core';\n";
        $content .= "import { Http, RequestOptions, Headers } from '@angular/http';\n";
        $content .= "import { Router } from '@angular/router';\n";
        $content .= "import { environment } from './../../../../environments/environment';\n";
        $content .= "import { ".$tableNameSingular." } from './../../../models/".strtoupper($moduleName)."/".$tableNameSingular."';\n\n";
        $content .= "@Injectable({\n";
        $content .= "   providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."Service {\n\n";
        $content .= "   url = environment.api_".strtolower($moduleName)." + '".strtolower($tableNameSingular)."/';\n";
        $content .= "   options = new RequestOptions();\n\n";
        $content .= "   constructor(private http: Http, private router: Router) {\n";
        $content .= "      this.options.headers = new Headers();\n";
        $content .= "      this.options.headers.append('api_token', sessionStorage.getItem('api_token'));\n";
        $content .= "   }\n\n";
        $content .= "   get(id?: number): Promise<any> {\n";
        $content .= "      if (typeof id === 'undefined') {\n";
        $content .= "         return this.http.get(this.url, this.options).toPromise()\n";
        $content .= "         .then( r => {\n";
        $content .= "            return r.json();\n";
        $content .= "         }).catch( error => { this.handledError(error.json());  });\n";
        $content .= "      }\n";
        $content .= "      return this.http.get(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   get_paginate(size: number, page: number): Promise<any> {\n";
        $content .= "      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json());  });\n";
        $content .= "   }\n\n";
        $content .= "   delete(id: number): Promise<any> {\n";
        $content .= "      return this.http.delete(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   getBackUp(): Promise<any> {\n";
        $content .= "      return this.http.get(this.url + 'backup', this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   post(".strtolower($tableNameSingular).": ".$tableNameSingular."): Promise<any> {\n";
        $content .= "      return this.http.post(this.url, JSON.stringify(".strtolower($tableNameSingular)."), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   put(".strtolower($tableNameSingular).": ".$tableNameSingular."): Promise<any> {\n";
        $content .= "      return this.http.put(this.url, JSON.stringify(".strtolower($tableNameSingular)."), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   masiveLoad(data: any[]): Promise<any> {\n";
        $content .= "      return this.http.post(this.url + 'masive_load', JSON.stringify({data: data}), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n\n";
        $content .= "   handledError(error: any) {\n";
        $content .= "      console.log(error);\n";
        $content .= "      sessionStorage.clear();\n";
        $content .= "      this.router.navigate(['/login']);\n";
        $content .= "   }\n";
        $content .= "}";
        return ["Table"=>$tableNameSingular, "Content"=>$content];
    }

    private function saveRouterFile($args, $type) {
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/server/routes/web.php", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    private function saveMigrationFile($args, $type) {
        $fecha = date('Y_m_d');
        $table = $args['Table'];
        $migrationIndex = $args['MigrationIndex'];
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/server/database/migrations/".$fecha.'_'.$migrationIndex."_create_".$this->checkNames($table['namePlural'])."_table.php", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Table"=>$table['nameSingular'],"Status"=>$status];
        return $toReturn;
    }

    private function saveControllerFile($args, $type) {
        $table = $args['Table'];
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/server/app/Http/Controllers/CRUD/".$table['nameSingular']."Controller.php", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Table"=>$table['nameSingular'],"Status"=>$status];
        return $toReturn;
    }

    private function saveModelFile($args, $type) {
        $tableName = $args['Table'];
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/server/app/Models/".$tableName.".php", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Table"=>$tableName,"Status"=>$status];
        return $toReturn;
    }

    private function saveModelTSFile($args, $type, $moduleName) {
        $tableName = $args['Table'];
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/models/".strtoupper($moduleName)."/".$tableName.".ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $archivoMobile = fopen("./output-".$type."/mobile/src/app/models/".strtoupper($moduleName)."/".$tableName.".ts", "w");
            fwrite($archivoMobile, $content);
            fclose($archivoMobile);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Table"=>$tableName,"Status"=>$status];
        return $toReturn;
    }

    private function saveServicelTSFile($args, $type, $moduleName) {
        $tableName = $args['Table'];
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/services/CRUD/".strtoupper($moduleName)."/".strtolower($tableName).".service.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $archivoMobile = fopen("./output-".$type."/mobile/src/app/services/CRUD/".strtoupper($moduleName)."/".strtolower($tableName).".service.ts", "w");
            fwrite($archivoMobile, $content);
            fclose($archivoMobile);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Table"=>$tableName,"Status"=>$status];
        return $toReturn;
    }

    public function getTableDataFromFiles() {
        $toReturn = [];
        $files = $this->getFiles($this->Models_dir);
        foreach ($files as $file) {
            $readerResult = $this->readMyFile($file);
            array_push($toReturn,$this->getTableData($readerResult));
        }
        return $toReturn;
    }

    private function executeSQL($args) {
        $server = $args['server'];
        $user = $args['user'];
        $password = $args['password'];
        $database = $args['database'];
        $sql = $args['SQL'];
        $parametros = $args['parametros'];
        $Conexion = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $stmt = $Conexion->prepare($sql);
        $stmt->execute($parametros);
        $salida = array();
        $cuenta = $stmt->rowCount();
        if($cuenta>0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $salida[]=$row;
            }
        }else{
            $salida[]=$cuenta;
        }
        $Conexion = null;
        return $salida;
    }

    public function getTableDataFromBDD($args) {
        $server = $args['server'];
        $user = $args['user'];
        $password = $args['password'];
        $database = $args['database'];
        $respuestaTablas = $this->executeSQL(["server"=>$server,"user"=>$user,"password"=>$password,"database"=>$database,"SQL"=>"SHOW TABLES;","parametros"=>array()]);
        foreach($respuestaTablas as $SelectTabla) {
            $nombreTabla = $SelectTabla["Tables_in_".$database];
            $tablas[] = $this->estructuraTabla(array_merge($args,["nombreTabla"=>$nombreTabla]));
        }
        return $tablas;
    }

    private function estructuraTabla($args) {
        $server = $args['server'];
        $user = $args['user'];
        $password = $args['password'];
        $database = $args['database'];
        $nombreTabla = $args['nombreTabla'];
        $respuestaColumnas = $this->executeSQL(["server"=>$server,"user"=>$user,"password"=>$password,"database"=>$database,"SQL"=>"SHOW COLUMNS FROM $nombreTabla;","parametros"=>array()]);
        $columnas = array();
        foreach($respuestaColumnas as $SelectColumna) {
            $nombreColumna = $SelectColumna["Field"];
            $tipoColumna = explode("(",$SelectColumna["Type"])[0];
            switch ($tipoColumna) {
                case "varchar":
                    $tipoColumna="string";
                    break;
                case "datetime":
                    $tipoColumna="dateTime";
                    break;
                case "int":
                    $tipoColumna="integer";
                    break;
            }
            $size = explode(")",explode("(",$SelectColumna["Type"])[1])[0];
            if($nombreColumna !== 'id') {
                $columnas[] = ["name"=>$nombreColumna,"type"=>$tipoColumna, "size"=>$size, "group"=>"fillable", "canNull"=>true, "unique"=>false];
            }
        }
        $table=["nameSingular"=>$nombreTabla,"namePlural"=>$nombreTabla];
        return ["Table"=>$table,"Columns"=>$columnas, "RelationShip"=>array()];
    }

    public function saveControllerOf($args, $type) {
        $controller = $this->buildController($args);
        return $this->saveControllerFile($controller, $type);
    }

    public function saveMigrationOf($args, $type) {
        $migration = $this->buildMigration($args);
        return $this->saveMigrationFile($migration, $type);
    }

    public function saveModelOf($args, $type) {
        $model = $this->buildModel($args);
        return $this->saveModelFile($model, $type);
    }

    public function saveModelTSOf($args, $type, $moduleName) {
        $model = $this->buildModelTS($args);
        return $this->saveModelTSFile($model, $type, $moduleName);
    }

    public function saveServiceTSOf($args, $type, $moduleName) {
        $service = $this->buildServiceTS($args, $moduleName);
        return $this->saveServicelTSFile($service, $type, $moduleName);
    }

    public function saveRoutersOf($args) {
        $router = $this->buildRouterFile($args);
        $type = $args['type'];
        return $this->saveRouterFile($router, $type);
    }

    public function saveLayoutRoutingModuleOf($args) {
        $type = $args['type'];
        $routingModule = $this->buildLayoutRoutingModuleFile($args);
        return $this->saveLayoutRoutingModuleFile($routingModule, $type);
    }
    public function saveLayoutAuthServicesOf($args) { //edited
        $type = $args['type'];
        $environmentProd=$this->buildLayoutAuthServicesFile($args); 
        return $this->saveLayoutAuthServicesFile($environmentProd, $type);
    }

    public function saveLayoutProfilePictureServiceOf($args) { //edited
        $type = $args['type'];
        $environmentProd=$this->buildLayoutProfilePictureServiceFile($args); 
        return $this->saveLayoutProfilePictureServiceFile($environmentProd, $type);
    }

    public function saveLayoutUserServiceOf($args) { //edited
        $type = $args['type'];
        $environmentProd=$this->buildLayoutUserServiceFile($args); 
        return $this->saveLayoutUserServiceFile($environmentProd, $type);
    }

    public function saveLayoutEnvironmentsProdOf($args) { //edited
        $type = $args['type'];
        $environmentProd=$this->buildLayoutEnvironmentsProdFile($args); 
        return $this->saveLayoutEnvironmentsProdFile($environmentProd, $type);
    }
    
    public function saveLayoutEnvironmentsOf($args) { //edited
        $type = $args['type'];
        $environment=$this->buildLayoutEnvironmentsFile($args);
        return $this->saveLayoutEnvironmentsFile($environment, $type);
    }
    
    public function saveSideBarOf($args) {
        $sidebar = $this->buildSideBarFile($args);
        $type = $args['type'];
        return $this->saveSideBarFile($sidebar, $type);
    }

    public function saveMigrationOfMany2Many($args) {
        $relacion = $args['relacion'];
        $type = $args['type'];
        $migration = $this->buildMigrationMany2Many($relacion);
        return $this->saveMigrationFile($migration, $type);
    }

    public function saveLayoutAuthServicesFile($args, $type) {//edited
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/services/auth.service.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    public function saveLayoutProfilePictureServiceFile($args, $type) {//edited
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/services/profile/profilepicture.service.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    public function saveLayoutUserServiceFile($args, $type) {//edited
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/services/profile/user.service.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    private function saveLayoutEnvironmentsProdFile($args, $type){//edited
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/environments/environment.prod.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }
    
    private function saveLayoutEnvironmentsFile($args, $type){//edited
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/environments/environment.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }
    
    private function saveLayoutRoutingModuleFile($args, $type) {
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/layout-routing.module.ts", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    private function saveSideBarFile($args, $type) {
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/components/sidebar/sidebar.component.html", "w");
            $content = $args['Content'];
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        $toReturn = ["Status"=>$status];
        return $toReturn;
    }

    private function buildSideBarFile($args) {
        $models = $args['models'];
        $type = $args['type'];
        $moduleName = $args['moduleName'];
        if($type === 'bootstrap') {
            $content = "<nav class=\"sidebar\" [ngClass]=\"{ sidebarPushRight: isActive, collapsed: collapsed }\">\n";
            $content .= "   <div class=\"list-group\">\n";
            $content .= "      <a [routerLink]=\"['/main']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">\n";
            $content .= "         <i class=\"fas fa-tachometer-alt\"></i>&nbsp; <span>Main</span>\n";
            $content .= "      </a>\n";
            $content .= "      <div class=\"nested-menu\">\n";
            $content .= "         <a class=\"list-group-item\" (click)=\"addExpandClass('bdd ".strtolower($moduleName)."')\">\n";
            $content .= "            <span class=\"fas fa-database\"></span>&nbsp;BDD ".strtoupper($moduleName)."\n";
            $content .= "         </a>\n";
            $content .= "         <li class=\"nested\" [class.expand]=\"showMenu === 'bdd ".strtolower($moduleName)."'\">\n";
            $content .= "            <ul class=\"submenu\">\n";
            $content .= "\n\n               //".$moduleName."\n\n";
            foreach($models as $modelo) {
                $model = $modelo['Table']['nameSingular'];
                $content .= "               <li>\n";
                $content .= "                  <a [routerLink]=\"['/".$this->checkNames($model)."']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">".$model." </a>\n";
                $content .= "               </li>\n";
            }
            $content .= "            </ul>\n";
            $content .= "         </li>\n";
            $content .= "      </div>\n";
            $content .= "      <div class=\"nested-menu\">\n";
            $content .= "         <a class=\"list-group-item\" (click)=\"addExpandClass('profile')\">\n";
            $content .= "            <span><img class=\"rounded-circle\" src=\"{{profileImg}}\" width=\"32px\" height=\"32px\"></span>&nbsp;<small *ngIf=\"refreshUser()\">{{ user.name }}</small>\n";
            $content .= "         </a>\n";
            $content .= "         <li class=\"nested\" [class.expand]=\"showMenu === 'profile'\">\n";
            $content .= "            <ul class=\"submenu\">\n";
            $content .= "               <li>\n";
            $content .= "                  <a [routerLink]=\"['/profile']\" [routerLinkActive]=\"['router-link-active']\" class=\"list-group-item\">Perfil </a>\n";
            $content .= "               </li>\n";
            $content .= "               <li>\n";
            $content .= "                  <a [routerLink]=\"['/login']\" (click)=\"logOut()\"><span>&nbsp;Cerrar Sesión</span></a>\n";
            $content .= "               </li>\n";
            $content .= "            </ul>\n";
            $content .= "         </li>\n";
            $content .= "      </div>\n";
            $content .= "   </div>\n";
            $content .= "</nav>\n";
        }
        if($type === 'metro') {
            $content = "<div class=\"sidebar-header bg-darkGrayBlue fg-white\" data-image=\"images/sb-bg-1.jpg\">\n";
            $content .= "   <a class=\"fg-white sub-action\"[routerLink]=\"['/login']\" (click)=\"logOut()\">\n";
            $content .= "      <span class=\"mif-settings-power mif-2x\"></span>\n";
            $content .= "   </a>\n";
            $content .= "   <a class=\"avatar\" [routerLink]=\"['/profile']\" [routerLinkActive]=\"['router-link-active']\">\n";
            $content .= "      <img src=\"{{profileImg}}\"/>\n";
            $content .= "   </a>\n";
            $content .= "   <span class=\"title\" *ngIf=\"refreshUser()\">{{ user.name }}</span>\n";
            $content .= "   <span class=\"subtitle\"> 2019 © LSystems</span>\n";
            $content .= "</div>\n";
            $content .= "<ul class=\"sidebar-menu\">\n";
            $content .= "   <li class=\"group-title\">BDD</li>\n";
            foreach($models as $modelo) {
                $model = $modelo['Table']['nameSingular'];
                $content .= "   <li>\n";
                $content .= "      <a [routerLink]=\"['/".$this->checkNames($model)."']\" [routerLinkActive]=\"['router-link-active']\">".$model." </a>\n";
                $content .= "   </li>\n";
            }
            $content .= "</ul>\n";
        }
        return ["Content"=>$content];
    }
    
    public function buildLayoutAuthServicesFile($args) {//edited
        $moduleName = $args['moduleName'];
        $content = "import { Injectable } from '@angular/core';\n";
        $content .= "import { Http, RequestOptions, Headers } from '@angular/http';\n";
        $content .= "import { environment } from './../../environments/environment';\n";
        $content .= "\n";
        $content .= "@Injectable({\n";
        $content .= "  providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class AuthService {\n";
        $content .= "\n";
        $content .= "  constructor(private http: Http) { }\n";
        $content .= "  \n";
        $content .= "  login(email: String, password: String): Promise<any> {\n";
        $content .= "    const data = {email: email, password: password};\n";
        $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'login', JSON.stringify(data)).toPromise()\n";
        $content .= "    .then( r =>\n";
        $content .= "      r.json()\n";
        $content .= "    ).catch( error => {\n";
        $content .= "      error.json();\n";
        $content .= "    });\n";
        $content .= "  }\n";
        $content .= "  \n";
        $content .= "  register(name: String, email: String): Promise<any> {\n";
        $content .= "    const data = {name: name, email: email};\n";
        $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'register', JSON.stringify(data)).toPromise()\n";
        $content .= "    .then( r =>\n";
        $content .= "      r.json()\n";
        $content .= "    ).catch( error => {\n";
        $content .= "      error.json();\n";
        $content .= "    });\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  password_recovery_request(email: String): Promise<any> {\n";
        $content .= "    const data = {email: email};\n";
        $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'password_recovery_request', JSON.stringify(data)).toPromise()\n";
        $content .= "    .then( r =>\n";
        $content .= "      r.json()\n";
        $content .= "    ).catch( error => {\n";
        $content .= "      error.json();\n";
        $content .= "    });\n";
        $content .= "  }\n";
        $content .= "  \n";
        $content .= "  password_change(new_password: String): Promise<any> {\n";
        $content .= "    const data = {new_password: new_password};\n";
        $content .= "    const options = new RequestOptions();\n";
        $content .= "    options.headers = new Headers();\n";
        $content .= "    options.headers.append('api_token', sessionStorage.getItem('api_token'));\n";
        $content .= "    return this.http.post(environment.api_".strtolower($moduleName)." + 'user/password_change', JSON.stringify(data), options).toPromise()\n";
        $content .= "    .then( r =>\n";
        $content .= "      r.json()\n";
        $content .= "    ).catch( error => {\n";
        $content .= "      error.json();\n";
        $content .= "    });\n";
        $content .= "  }\n";
        $content .= "}\n";
        return ["Content"=>$content];
    }
    
    public function buildLayoutProfilePictureServiceFile($args) {//edited
        $moduleName = $args['moduleName'];
        $content = "import { Injectable } from '@angular/core';\n";
        $content .= "import { Http, RequestOptions, Headers } from '@angular/http';\n";
        $content .= "import { environment } from './../../../environments/environment';\n";
        $content .= "import { ProfilePicture } from './../../models/profile/ProfilePicture';\n";
        $content .= "\n";
        $content .= "@Injectable({\n";
        $content .= "providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class ProfilePictureService {\n";
        $content .= "\n";
        $content .= "   url = environment.api_".strtolower($moduleName)." + 'profilepicture/';\n";
        $content .= "   options = new RequestOptions();\n";
        $content .= "   \n";
        $content .= "   constructor(private http: Http) {\n";
        $content .= "      this.options.headers = new Headers();\n";
        $content .= "      this.options.headers.append('api_token', sessionStorage.getItem('api_token'));\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   get(token?): Promise<any> {\n";
        $content .= "      if ( typeof token !== 'undefined') {\n";
        $content .= "         this.options.headers = new Headers();\n";
        $content .= "         this.options.headers.append('api_token', token);\n";
        $content .= "      }\n";
        $content .= "      return this.http.get(this.url, this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   get_paginate(size: number, page: number): Promise<any> {\n";
        $content .= "      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   delete(id: number): Promise<any> {\n";
        $content .= "      return this.http.delete(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   post(profilepicture: ProfilePicture): Promise<any> {\n";
        $content .= "      return this.http.post(this.url, JSON.stringify(profilepicture), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   put(profilepicture: ProfilePicture): Promise<any> {\n";
        $content .= "      return this.http.put(this.url, JSON.stringify(profilepicture), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   handledError(error: any) {\n";
        $content .= "      console.log(error);\n";
        $content .= "   }\n";
        $content .= "}\n";    
        return ["Content"=>$content];
    }
    
    public function buildLayoutUserServiceFile($args) {//edited
        $moduleName = $args['moduleName'];
        $content = "import { Injectable } from '@angular/core';\n";
        $content .= "import { Http, RequestOptions, Headers } from '@angular/http';\n";
        $content .= "import { environment } from './../../../environments/environment';\n";
        $content .= "import { User } from './../../models/profile/User';\n";
        $content .= "\n";
        $content .= "@Injectable({\n";
        $content .= "   providedIn: 'root'\n";
        $content .= "})\n";
        $content .= "export class UserService {\n";
        $content .= "\n";
        $content .= "   url = environment.api_".strtolower($moduleName)." + 'user/';\n";
        $content .= "   options = new RequestOptions();\n";
        $content .= "   \n";
        $content .= "   constructor(private http: Http) {\n";
        $content .= "      this.options.headers = new Headers();\n";
        $content .= "      this.options.headers.append('api_token', sessionStorage.getItem('api_token'));\n";
        $content .= "   }\n";
        $content .= "\n";
        $content .= "   get(id?: number): Promise<any> {\n";
        $content .= "      if (typeof id === 'undefined') {\n";
        $content .= "         return this.http.get(this.url, this.options).toPromise()\n";
        $content .= "         .then( r => {\n";
        $content .= "            return r.json();\n";
        $content .= "         }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "      }\n";
        $content .= "      return this.http.get(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   get_paginate(size: number, page: number): Promise<any> {\n";
        $content .= "      return this.http.get(this.url + 'paginate?size=' + size.toString() + '&page=' + page.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   delete(id: number): Promise<any> {\n";
        $content .= "      return this.http.delete(this.url + '?id=' + id.toString(), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   post(user: User): Promise<any> {\n";
        $content .= "      return this.http.post(this.url, JSON.stringify(user), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   put(user: User): Promise<any> {\n";
        $content .= "      return this.http.put(this.url, JSON.stringify(user), this.options).toPromise()\n";
        $content .= "      .then( r => {\n";
        $content .= "         return r.json();\n";
        $content .= "      }).catch( error => { this.handledError(error.json()); });\n";
        $content .= "   }\n";
        $content .= "   \n";
        $content .= "   handledError(error: any) {\n";
        $content .= "      console.log(error);\n";
        $content .= "   }\n";
        $content .= "}\n";
        return ["Content"=>$content];
    }

    private function buildLayoutEnvironmentsProdFile($args) { //edited
        $moduleName = $args['moduleName'];
        $content = "export const environment = {\n";
        $content .= "production: true,\n";
        $content .= "api_".strtolower($moduleName).": 'http://localhost:8000/',\n";
        $content .= "gmapapiKey: 'AIzaSyCZQgG8L6ntkJZarveWX9mvy9f9MMOoNDA',\n";
        $content .= "};";
        return ["Content"=>$content];
    }

    private function buildLayoutEnvironmentsFile($args) { //edited
        $moduleName = $args['moduleName'];
        $content = "export const environment = {\n";
        $content .="production: false,\n";
        $content .="api_".strtolower($moduleName).": 'http://localhost:8000/',\n";
        $content .="gmapapiKey: 'AIzaSyCZQgG8L6ntkJZarveWX9mvy9f9MMOoNDA',\n";
        $content .="};";
        return ["Content"=>$content];
    }

    private function buildLayoutRoutingModuleFile($args) {
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
            $content .= "            path: '".$this->checkNames($model)."',\n";
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

    public function cleanInput() { //edited
      foreach(glob("input/Models/*.*") as $fileToDelete) {unlink($fileToDelete);};
    }

    private function cleanOutput($type) { //edited
        foreach(glob("output-".$type."/server/app/Http/Controllers/CRUD/*php") as $fileToDelete) {unlink($fileToDelete);};
        foreach(glob("output-".$type."/server/app/Models/*.php") as $fileToDelete) {unlink($fileToDelete);};
        foreach(glob("output-".$type."/server/database/migrations/*.*") as $fileToDelete) {unlink($fileToDelete);};
        $this->force_rmdir("output-".$type."/client/src/app/models/");
        $this->force_rmdir("output-".$type."/client/src/app/services/CRUD/");
        $this->force_rmdir("output-".$type."/mobile/src/app/models/");
        $this->force_rmdir("output-".$type."/mobile/src/app/services/CRUD/");
        mkdir("output-".$type."/client/src/app/models/", 0777, true);
        mkdir("output-".$type."/mobile/src/app/models/", 0777, true);
        mkdir("output-".$type."/client/src/app/services/CRUD/", 0777, true);
        mkdir("output-".$type."/mobile/src/app/services/CRUD/", 0777, true);
        unlink("output.zip");
        $this->force_rmdir("output-".$type."/client/src/app/layout/CRUD/");
        @mkdir("output-".$type."/mobile/src/app/services/CRUD/", 0777, true);
        //   $myFile=fopen(".gitkeep","a");
        //   fwrite ($myFile, date("D m/d/Y"));
        //   fclose($myFile);
        $this->copyFullPath("migrationsProfile/","output-".$type."/server/database/migrations/");
        $this->copyFullPath("models/","output-".$type."/client/src/app/models/");
        $this->copyFullPath("models/","output-".$type."/mobile/src/app/models/");
    }

    function force_rmdir($path){ //edited
        foreach(glob($path . "/*") as $fileToDelete){
        if (is_dir($fileToDelete)){
          $this->force_rmdir($fileToDelete);
        } else {
        unlink($fileToDelete);
        }
      }
      rmdir($path);
    }

    function copyAllFiles($src,$dst) { //edited
      $dir = opendir($src);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
          if ( !is_dir($src . '/' . $file) ) {
            copy($src . '/' . $file,$dst . '/' . $file);
          }

        }
      }
      closedir($dir);
    }

    function copyFullPath($src,$dst) { //edited
      $dir = opendir($src);
      @mkdir($dst);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
          if ( is_dir($src . '/' . $file) ) {
            $this->copyFullPath($src . '/' . $file,$dst . '/' . $file);
          }
          else {
            copy($src . '/' . $file,$dst . '/' . $file);
          }
        }
      }
      closedir($dir);
    }

    public function getFromOutput($args) {
        $type = $args['q'];
        $zip = new ZipArchive();
        $zip->open('output.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $rootPath = realpath("output-".$type);
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        header('Content-disposition: attachment; filename=output.zip');
        header('Content-Type: application/zip');
        readfile('output.zip');
        $this->cleanOutput($type);
    }

    protected function makeDirs($args) { // edited
        $moduleName = $args['moduleName'];
        $type = $args['type'];
        @mkdir("output-".$type."/client/src/app/models/".strtoupper($moduleName), 0777, true);
        @mkdir("output-".$type."/client/src/app/services/CRUD/".strtoupper($moduleName), 0777, true);
        @mkdir("output-".$type."/mobile/src/app/models/".strtoupper($moduleName), 0777, true);
        @mkdir("output-".$type."/mobile/src/app/services/CRUD/".strtoupper($moduleName), 0777, true);
        @mkdir("output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName), 0777, true);
    }

    public function buildAll($args) {
        $modelos = $args['models'];
        $type = $args['type'];
        $moduleName = $args['moduleName'];
        $pages = $args['pages'];
        $this->saveRoutersOf($args);
        $this->saveLayoutEnvironmentsProdOf($args);//edited
        $this->saveLayoutEnvironmentsOf($args);//edited
        $this->saveLayoutAuthServicesOf($args);//edited
        $this->saveLayoutProfilePictureServiceOf($args);//edited
        $this->saveLayoutUserServiceOf($args);//edited
        $this->saveLayoutRoutingModuleOf($args);
        $this->saveSideBarOf($args);
        $this->makeDirs($args);
        $relaciones = $args['relationships'];
        $log = [];
        foreach ($modelos as $modelo) {
            $ResponseModel = $this->saveModelOf($modelo, $type);
            $ResponseMigration = $this->saveMigrationOf($modelo, $type);
            $ResponseController = $this->saveControllerOf($modelo, $type);
            $ResponseModelTS = $this->saveModelTSOf($modelo, $type, $moduleName);
            $ResponseServiceTS = $this->saveServiceTSOf($modelo, $type, $moduleName);
            $ResponseLayout = $this->saveLayoutOf($modelo, $type, $moduleName);
            $register = ["Name"=>$modelo['Table']['nameSingular'], "Model_File"=>$ResponseModel, "Migration_File"=>$ResponseMigration, "Controller_File"=>$ResponseController, "ModelTS_File"=>$ResponseModelTS, "ServiceTS_File"=>$ResponseServiceTS, "Client_Layout"=>$ResponseLayout];
            array_push($log,$register);
        }
        return $log;
    }

    public function saveLayoutOf($args, $type, $moduleName) {
        $folderStatus = $this->createFolder($args, $type, $moduleName);
        $statusRoutingModule = $this->createRoutingModule($args, $type, $moduleName);
        $statusComponentSCSS = $this->createComponentSCSS($args, $type, $moduleName);
        $statusComponentSpec = $this->createComponentSpec($args, $type, $moduleName);
        $statusModuleSpec = $this->createModuleSpec($args, $type, $moduleName);
        $statusModule = $this->createModule($args, $type, $moduleName);
        $statusComponent = $this->createComponent($args, $type, $moduleName);
        $statusComponentHtml = $this->createComponentHtml($args, $type, $moduleName);
        $status = 'Success';
        $tableName = $args['Table']['nameSingular'];
        $toReturn = ["Table"=>$tableName,"Status"=>$status];
        return $toReturn;
    }

    private function createFolder($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        @mkdir("output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular,0777, true);
        return true;
    }

    private function createRoutingModule($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { NgModule } from '@angular/core';\n";
        $content .= "import { RouterModule, Routes } from '@angular/router';\n";
        $content .= "import { ".$tableNameSingular."Component } from './".strtolower($tableNameSingular).".component';\n\n";
        $content .= "const routes: Routes = [\n";
        $content .= "   {\n";
        $content .= "      path: '',\n";
        $content .= "      component: ".$tableNameSingular."Component\n";
        $content .= "   }\n";
        $content .= "];\n\n";
        $content .= "@NgModule({\n";
        $content .= "   imports: [RouterModule.forChild(routes)],\n";
        $content .= "   exports: [RouterModule]\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."RoutingModule {}\n";
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular)."-routing.module.ts", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function createComponentSCSS($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableColumns = $args['Columns'];
        $content = " ";
        $tieneBoolean = false;
        foreach ($tableColumns as $column) {
            if ($column['type']==='boolean') {
                $tieneBoolean = true;
            }
        }
        if($tieneBoolean && $type === 'bootstrap') {
            $content = ".switch {\n";
            $content .= "   position: relative;\n";
            $content .= "   display: inline-block;\n";
            $content .= "   width: 60px;\n";
            $content .= "   height: 34px;\n";
            $content .= "}\n\n";
            $content .= ".switch input {\n";
            $content .= "   display: none;\n";
            $content .= "}\n\n";
            $content .= ".slider {\n";
            $content .= "   position: absolute;\n";
            $content .= "   cursor: pointer;\n";
            $content .= "   top: 0;\n";
            $content .= "   left: 0;\n";
            $content .= "   right: 0;\n";
            $content .= "   bottom: 0;\n";
            $content .= "   background-color: #ccc;\n";
            $content .= "   -webkit-transition: 0.4s;\n";
            $content .= "   transition: 0.4s;\n";
            $content .= "}\n\n";
            $content .= ".slider:before {\n";
            $content .= "   position: absolute;\n";
            $content .= "   content: \"\";\n";
            $content .= "   height: 26px;\n";
            $content .= "   width: 26px;\n";
            $content .= "   left: 4px;\n";
            $content .= "   bottom: 4px;\n";
            $content .= "   background-color: white;\n";
            $content .= "   -webkit-transition: 0.4s;\n";
            $content .= "   transition: 0.4s;\n";
            $content .= "}\n\n";
            $content .= "input:checked + .slider {\n";
            $content .= "   background-color: #218838;\n";
            $content .= "}\n\n";
            $content .= "input:focus + .slider {\n";
            $content .= "   box-shadow: 0 0 1px #218838;\n";
            $content .= "}\n\n";
            $content .= "input:checked + .slider:before {\n";
            $content .= "   -webkit-transform: translateX(26px);\n";
            $content .= "   -ms-transform: translateX(26px);\n";
            $content .= "   transform: translateX(26px);\n";
            $content .= "}\n\n";
            $content .= ".slider.round {\n";
            $content .= "   border-radius: 34px;\n";
            $content .= "}\n\n";
            $content .= ".slider.round:before {\n";
            $content .= "   border-radius: 50%;\n";
            $content .= "}\n\n";
        }
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.scss", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function createComponentSpec($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { async, ComponentFixture, TestBed } from '@angular/core/testing';\n";
        $content .= "import { ".$tableNameSingular."Component } from './".strtolower($tableNameSingular).".component';\n\n";
        $content .= "describe('".$tableNameSingular."Component', () => {\n";
        $content .= "   let component: ".$tableNameSingular."Component;\n";
        $content .= "   let fixture: ComponentFixture<".$tableNameSingular."Component>;\n\n";
        $content .= "   beforeEach(async(() => {\n";
        $content .= "      TestBed.configureTestingModule({\n";
        $content .= "         declarations: [".$tableNameSingular."Component]\n";
        $content .= "      }).compileComponents();\n";
        $content .= "   }));\n\n";
        $content .= "   beforeEach(() => {\n";
        $content .= "      fixture = TestBed.createComponent(".$tableNameSingular."Component);\n";
        $content .= "      component = fixture.componentInstance;\n";
        $content .= "      fixture.detectChanges();\n";
        $content .= "   });\n\n";
        $content .= "   it('should create', () => {\n";
        $content .= "      expect(component).toBeTruthy();\n";
        $content .= "   });\n";
        $content .= "});";
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.spec.ts", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function createModuleSpec($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $content = "import { ".$tableNameSingular."Module } from './".strtolower($tableNameSingular).".module';\n\n";
        $content .= "describe('".$tableNameSingular."Module', () => {\n";
        $content .= "   let blackPageModule: ".$tableNameSingular."Module;\n\n";
        $content .= "   beforeEach(() => {\n";
        $content .= "      blackPageModule = new ".$tableNameSingular."Module();";
        $content .= "   });\n\n";
        $content .= "   it('should create an instance', () => {\n";
        $content .= "      expect(blackPageModule).toBeTruthy();\n";
        $content .= "   });\n";
        $content .= "});";
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".module.spec.ts", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function createComponentHtml($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $tableNamePlural = $args['Table']['namePlural'];
        $tableColumns = $args['Columns'];
        $relationships = $args['RelationShip'];
        $esAdjunto = $args['esAdjunto'];
        $content = "";
        if($type === 'bootstrap') {
            $content = $this->bodyBootstrap($args);
        }
        if($type === 'metro') {
            $content = $this->bodyMetro($args);
        }
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.html", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    protected function bodyBootstrap($args) {
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
                $content .= "               <th>".$this->checkNames($column['name'])."</th>\n";
            }
        }
        if ($esAdjunto) {
            $content .= "               <th>Opciones</th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </thead>\n";
        $content .= "         <tbody>\n";
        $content .= "            <tr *ngFor=\"let ".$this->checkNames($tableNameSingular)." of ".$this->checkNames($tableNamePlural)."\" (click)=\"select".$tableNameSingular."(".$this->checkNames($tableNameSingular).")\">\n";
        $content .= "               <td class=\"text-right\"><span *ngIf=\"".$this->checkNames($tableNameSingular)."Selected === ".$this->checkNames($tableNameSingular)."\" class=\"far fa-hand-point-right\"></span></td>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if($column['type']==="gmap") {
                    $content .= "               <td>Lat: {{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."_latitude}} Lng: {{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."_longitude}}</td>\n";
                }else {
                    $content .= "               <td>{{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."}}</td>\n";
                }
            }
        }
        if ($esAdjunto) {
            $content .= "               <th><button type=\"button\" title=\"Descargar\" class=\"btn btn-success\" (click)=\"downloadFile(".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file, ".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file_type, ".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file_name)\"><i class=\"fas fa-download\"></i></button></th>\n";
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
                $content .= "               <label for=\"".$this->checkNames($column['name'])."\" class=\"col-4 col-form-label\">".$this->checkNames($column['name'])."</label>\n";
                $content .= "               <div class=\"col-8\">\n";
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "                  <input type=\"date\" class=\"form-control\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [ngModel]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])." | date:'y-MM-dd'\" (ngModelChange)=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])." = \$event\">\n";
                } else {
                    if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                        $content .= "                  <input type=\"number\" class=\"form-control\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\">\n";
                    } else {
                        if ($column['type']==='longText' && !$esAdjunto) {
                            $content .= "                  <ck-editor id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" skin=\"moono-lisa\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\"></ck-editor>\n";
                        } else {
                            if ($column['type']==='longText' && $esAdjunto) {
                                $content .= "                  <input type=\"file\" class=\"form-control\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" (change)=\"CodeFile".$tableNameSingular."(\$event)\">\n";
                            } else {
                                if ($column['type']==='boolean') {
                                    $content .= "                  <label class=\"switch\"><input type=\"checkbox\"id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\"><span class=\"slider round\"></span></label>\n";
                                } else {
                                    if ($column['type']==='gmap') {
                                        $content .= "                  <agm-map class=\"col-12\" style=\"height: 200px;\"[latitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_latitude * 1\" [longitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_longitude * 1\" [zoom]=\"15\" (mapClick)=\"".$this->checkNames($column['name'])."Event(\$event)\">\n";
                                        $content .= "                     <agm-marker [latitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_latitude * 1\" [longitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_longitude * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".$this->checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                        $content .= "                  </agm-map>\n";
                                    } else {
                                        $content .= "                  <input type=\"text\" class=\"form-control\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\">\n";
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
                    $content .= "               <label for=\"".$this->checkNames($relationship['fromSingular'])."_id\" class=\"col-4 col-form-label\">".$relationship['fromSingular']."</label>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <select class=\"form-control\" id=\"".$this->checkNames($relationship['fromSingular'])."_id\" name=\"".$this->checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromSingular'])."_id\">\n";
                    $content .= "                     <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                     <option *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($relationship['fromPlural'])."\" value={{".$this->checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                        {{".$this->checkNames($relationship['fromSingular']).".id}}\n";
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
                    $content .= "                     <select class=\"form-control\" id=\"".$this->checkNames($relationship['fromSingular'])."_id\" name=\"".$this->checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId\">\n";
                    $content .= "                        <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                        <option *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($relationship['fromPlural'])."\" value={{".$this->checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                           {{".$this->checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                        </option>\n";
                    $content .= "                     </select>\n";
                    $content .= "                  </div>\n";
                    $content .= "               </div>\n";
                    $content .= "               <div class=\"col-4\">\n";
                    $content .= "               </div>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <table class=\"table table-hover mt-2\">\n";
                    $content .= "                     <tbody>\n";
                    $content .= "                        <tr *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."\" (click)=\"select".$relationship['fromSingular']."(".$this->checkNames($relationship['fromSingular']).")\">\n";
                    $content .= "                           <td class=\"text-right\"><span *ngIf=\"".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId === ".$this->checkNames($relationship['fromSingular']).".id\" class=\"far fa-hand-point-right\"></span></td>\n";
                    $content .= "                           <td>{{".$this->checkNames($relationship['fromSingular']).".id}}</td>\n";
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
                    $content .= "               <label for=\"".$this->checkNames($relationship['fromSingular'])."_id\" class=\"col-4 col-form-label\">".$relationship['fromSingular']."</label>\n";
                    $content .= "               <div class=\"col-8\">\n";
                    $content .= "                  <select class=\"form-control\" id=\"".$this->checkNames($relationship['toSingular'])."_id\" name=\"".$this->checkNames($relationship['toSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['toSingular'])."_id\">\n";
                    $content .= "                     <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                     <option *ngFor=\"let ".$this->checkNames($relationship['toSingular'])." of ".$this->checkNames($relationship['toPlural'])."\" value={{".$this->checkNames($relationship['toSingular']).".id}}>\n";
                    $content .= "                        {{".$this->checkNames($relationship['toSingular']).".id}}\n";
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

    protected function bodyMetro($args) {
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
                $content .= "               <th>".$this->checkNames($column['name'])."</th>\n";
            }
        }
        if ($esAdjunto) {
            $content .= "               <th>Opciones</th>\n";
        }
        $content .= "            </tr>\n";
        $content .= "         </thead>\n";
        $content .= "         <tbody>\n";
        $content .= "            <tr *ngFor=\"let ".$this->checkNames($tableNameSingular)." of ".$this->checkNames($tableNamePlural)."\" (click)=\"select".$tableNameSingular."(".$this->checkNames($tableNameSingular).")\">\n";
        $content .= "               <td class=\"text-right\"><span *ngIf=\"".$this->checkNames($tableNameSingular)."Selected === ".$this->checkNames($tableNameSingular)."\" class=\"far fa-hand-point-right\"></span></td>\n";
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if($column['type']==="gmap") {
                    $content .= "               <td>Lat: {{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."_latitude}} Lng: {{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."_longitude}}</td>\n";
                }else {
                    $content .= "               <td>{{".$this->checkNames($tableNameSingular).".".$this->checkNames($column['name'])."}}</td>\n";
                }
            }
        }
        if ($esAdjunto) {
            $content .= "               <th><button type=\"button\" title=\"Descargar\" class=\"button success\" (click)=\"downloadFile(".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file, ".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file_type, ".$this->checkNames($tableNameSingular).".".$this->checkNames($tableNameSingular)."_file_name)\"><i class=\"fas fa-download\"></i></button></th>\n";
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
                $content .= "                        <label for=\"".$this->checkNames($column['name'])."\">".$this->checkNames($column['name'])."</label>\n";
                if ($column['type']==='date' || $column['type']==='dateTime' || $column['type']==='dateTimeTz') {
                    $content .= "                        <input type=\"date\" data-role=\"input\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [ngModel]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])." | date:'y-MM-dd'\" (ngModelChange)=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])." = \$event\">\n";
                } else {
                    if ($column['type']==='integer' || $column['type']==='smallIncrements' || $column['type']==='smallInteger' || $column['type']==='decimal' || $column['type']==='double' || $column['type']==='bigInteger' || $column['type']==='binary' || $column['type']==='float' || $column['type']==='unsignedBigInteger' || $column['type']==='unsignedDecimal' || $column['type']==='unsignedInteger' || $column['type']==='unsignedMediumInteger' || $column['type']==='unsignedSmallInteger' || $column['type']==='unsignedTinyInteger') {
                        $content .= "                        <input type=\"number\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\">\n";
                    } else {
                        if ($column['type']==='longText' && !$esAdjunto) {
                            $content .= "                        <ck-editor id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" skin=\"moono-lisa\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\"></ck-editor>\n";
                        } else {
                            if ($column['type']==='longText' && $esAdjunto) {
                                $content .= "                        <input type=\"file\" data-role=\"file\" data-button-title=\"<span class='mif-folder'></span>\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" (change)=\"CodeFile".$tableNameSingular."(\$event)\">\n";
                            } else {
                                if ($column['type']==='boolean') {
                                    $content .= "                        <input type=\"checkbox\" data-role=\"switch\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\">\n";
                                } else {
                                    if ($column['type']==='gmap') {
                                        $content .= "                        <agm-map class=\"cell-12\" style=\"height: 200px;\"[latitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_latitude * 1\" [longitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_longitude * 1\" [zoom]=\"15\" (mapClick)=\"".$this->checkNames($column['name'])."Event(\$event)\">\n";
                                        $content .= "                           <agm-marker [latitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_latitude * 1\" [longitude]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_longitude * 1\" [markerDraggable]=\"true\" (dragEnd)=\"".$this->checkNames($column['name'])."Event(\$event)\" [animation]=\"'DROP'\"></agm-marker>\n";
                                        $content .= "                        </agm-map>\n";
                                    } else {
                                        $content .= "                        <input type=\"text\" id=\"".$this->checkNames($column['name'])."\" name=\"".$this->checkNames($column['name'])."\" placeholder=\"".$column['name']."\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."\">\n";
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
                    $content .= "                        <label for=\"".$this->checkNames($relationship['fromSingular'])."_id\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".$this->checkNames($relationship['fromSingular'])."_id\" name=\"".$this->checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromSingular'])."_id\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($relationship['fromPlural'])."\" value={{".$this->checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                              {{".$this->checkNames($relationship['fromSingular']).".id}}\n";
                    $content .= "                           </option>\n";
                    $content .= "                        </select>\n";
                    $content .= "                     </div>\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label class=\"cell-12\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".$this->checkNames($relationship['fromSingular'])."_id\" name=\"".$this->checkNames($relationship['fromSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($relationship['fromPlural'])."\" value={{".$this->checkNames($relationship['fromSingular']).".id}}>\n";
                    $content .= "                              {{".$this->checkNames($relationship['fromSingular']).".id}}\n";
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
                    $content .= "                              <tr *ngFor=\"let ".$this->checkNames($relationship['fromSingular'])." of ".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])."\" (click)=\"select".$relationship['fromSingular']."(".$this->checkNames($relationship['fromSingular']).")\">\n";
                    $content .= "                                 <td class=\"text-right\"><span *ngIf=\"".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId === ".$this->checkNames($relationship['fromSingular']).".id\" class=\"far fa-hand-point-right\"></span></td>\n";
                    $content .= "                                 <td>{{".$this->checkNames($relationship['fromSingular']).".id}}</td>\n";
                    $content .= "                              </tr>\n";
                    $content .= "                           </tbody>\n";
                    $content .= "                        </table>\n";
                    $content .= "                     </div>\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "                     <div class=\"form-group row\">\n";
                    $content .= "                        <label for=\"".$this->checkNames($relationship['fromSingular'])."_id\">".$relationship['fromSingular']."</label>\n";
                    $content .= "                        <select id=\"".$this->checkNames($relationship['toSingular'])."_id\" name=\"".$this->checkNames($relationship['toSingular'])."_id\" [(ngModel)]=\"".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['toSingular'])."_id\">\n";
                    $content .= "                           <option value=\"0\" selected>Seleccione...</option>\n";
                    $content .= "                           <option *ngFor=\"let ".$this->checkNames($relationship['toSingular'])." of ".$this->checkNames($relationship['toPlural'])."\" value={{".$this->checkNames($relationship['toSingular']).".id}}>\n";
                    $content .= "                              {{".$this->checkNames($relationship['toSingular']).".id}}\n";
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

    private function createComponent($args, $type, $moduleName) {
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
                    $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/profile/".strtolower($relationship['fromSingular']).".service';\n";
                    $content .= "import { ".$relationship['fromSingular']." } from './../../../../models/profile/".$relationship['fromSingular']."';\n\n";
                } else {
                    if ($relationship['kind'] === 'many2one') {
                        $content .= "import { ".$relationship['toSingular']."Service } from './../../../../services/CRUD/".strtoupper($moduleName)."/".strtolower($relationship['toSingular']).".service';\n";
                        $content .= "import { ".$relationship['toSingular']." } from './../../../../models/".$relationship['toSingular']."';\n";
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
        $content .= "   ".$this->checkNames($tableNamePlural).": ".$tableNameSingular."[] = [];\n";
        $content .= "   ".$this->checkNames($tableNameSingular)."Selected: ".$tableNameSingular." = new ".$tableNameSingular."();\n\n";
        $content .= "   currentPage = 1;\n";
        $content .= "   lastPage = 1;\n";
        $content .= "   showDialog = false;\n";
        $content .= "   recordsByPage = 5;\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "   ".$this->checkNames($relationship['fromPlural']).": ".$relationship['fromSingular']."[] = [];\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "   ".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId: number;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   ".$this->checkNames($relationship['toPlural']).": ".$relationship['toSingular']."[] = [];\n";
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
                    $content .= "               private ".$this->checkNames($relationship['fromSingular'])."DataService: ".$relationship['fromSingular']."Service,\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "               private ".$this->checkNames($relationship['toSingular'])."DataService: ".$relationship['toSingular']."Service,\n";
                }
            }
        }
        $content .= "               private ".$this->checkNames($tableNameSingular)."DataService: ".$tableNameSingular."Service) {}\n\n";
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
            $content .= "            this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($tableNameSingular."FileName")." = file.name;\n";
            $content .= "            this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($tableNameSingular."FileType")." = file.type;\n";
            $content .= "            this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($tableNameSingular."File")." = reader.result.toString().split(',')[1];\n";
            $content .= "         };\n";
            $content .= "      }\n";
            $content .= "   }\n\n";
        }
        $content .= "   select".$tableNameSingular."(".$this->checkNames($tableNameSingular).": ".$tableNameSingular.") {\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."Selected = ".$this->checkNames($tableNameSingular).";\n";
        $content .= "   }\n\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many' || $relationship['kind'] === 'many2many') {
                    $content .= "   get".$relationship['fromSingular']."() {\n";
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])." = [];\n";
                    $content .= "      this.".$this->checkNames($relationship['fromSingular'])."DataService.get().then( r => {\n";
                    $content .= "         this.".$this->checkNames($relationship['fromPlural'])." = r as ".$relationship['fromSingular']."[];\n";
                    $content .= "      }).catch( e => console.log(e) );\n";
                    $content .= "   }\n\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "   get".$relationship['fromPlural']."On".$relationship['toSingular']."() {\n";
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = [];\n";
                    $content .= "      this.".$this->checkNames($tableNameSingular)."DataService.get(this.".$this->checkNames($tableNameSingular)."Selected.id).then( r => {\n";
                    $content .= "         this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = r.attach[0].".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." as ".$relationship['fromSingular']."[];\n";
                    $content .= "      }).catch( e => console.log(e) );\n";
                    $content .= "   }\n\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "   get".$relationship['toSingular']."() {\n";
                    $content .= "      this.".$this->checkNames($relationship['toPlural'])." = [];\n";
                    $content .= "      this.".$this->checkNames($relationship['toSingular'])."DataService.get().then( r => {\n";
                    $content .= "         this.".$this->checkNames($relationship['toPlural'])." = r as ".$relationship['toSingular']."[];\n";
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
                $content .= "   ".$this->checkNames($column['name'])."Event(event) {\n";
                $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_latitude = event.coords.lat;\n";
                $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($column['name'])."_longitude = event.coords.lng;\n";
                $content .= "   }\n\n";
            }
        }
        $content .= "   get".$tableNamePlural."() {\n";
        $content .= "      this.".$this->checkNames($tableNamePlural)." = [];\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."Selected = new ".$tableNameSingular."();\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromSingular'])."_id = 0;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['toSingular'])."_id = 0;\n";
                }
            }
        }
        $content .= "      this.".$this->checkNames($tableNameSingular)."DataService.get_paginate(this.recordsByPage, this.currentPage).then( r => {\n";
        $content .= "         this.".$this->checkNames($tableNamePlural)." = r.data as ".$tableNameSingular."[];\n";
        $content .= "         this.lastPage = r.last_page;\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   new".$tableNameSingular."(content) {\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."Selected = new ".$tableNameSingular."();\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'one2one' || $relationship['kind'] === 'one2many') {
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromSingular'])."_id = 0;\n";
                }
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['toSingular'])."_id = 0;\n";
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
                    $content .= "      if ( typeof this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." === 'undefined' ) {\n";
                    $content .= "         this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = [];\n";
                    $content .= "      }\n";
                }
            }
        }
        $content .= "      if (typeof this.".$this->checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
        $content .= "         this.toastr.errorToastr('Debe seleccionar un registro.', 'Error');\n";
        $content .= "         return;\n";
        $content .= "      }\n";
        foreach ($relationships as $relationship) {
            if ($relationship['toSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2many') {
                    $content .= "      this.get".$relationship['fromPlural']."On".$relationship['toSingular']."();\n";
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = 0;\n";
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
        $content .= "      if (typeof this.".$this->checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
        $content .= "         this.toastr.errorToastr('Debe seleccionar un registro.', 'Error');\n";
        $content .= "         return;\n";
        $content .= "      }\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."DataService.delete(this.".$this->checkNames($tableNameSingular)."Selected.id).then( r => {\n";
        $content .= "         this.toastr.successToastr('Registro Borrado satisfactoriamente.', 'Borrar');\n";
        $content .= "         this.get".$tableNamePlural."();\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   backup() {\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."DataService.getBackUp().then( r => {\n";
        $content .= "         const backupData = r;\n";
        $content .= "         const blob = new Blob([JSON.stringify(backupData)], { type: 'text/plain' });\n";
        $content .= "         const fecha = new Date();\n";
        $content .= "         saveAs(blob, fecha.toLocaleDateString() + '_".$tableNamePlural.".json');\n";
        $content .= "      }).catch( e => console.log(e) );\n";
        $content .= "   }\n\n";
        $content .= "   toCSV() {\n";
        $content .= "      this.".$this->checkNames($tableNameSingular)."DataService.get().then( r => {\n";
        $content .= "         const backupData = r as ".$tableNameSingular."[];\n";
        $colsHeader = "id;";
        $colsRows = "element.id; + ";
        foreach ($tableColumns as $column) {
            if($column['type']==="gmap") {
                $colsHeader .= $this->checkNames($column['name'])."_latitude;";
                $colsHeader .= $this->checkNames($column['name'])."_longitude;";
                $colsRows .= "element.".$this->checkNames($column['name'])."_latitude + ';' + ";
                $colsRows .= "element.".$this->checkNames($column['name'])."_longitude + ';' + ";
            }else {
                $colsHeader .= $this->checkNames($column['name']).";";
                $colsRows .= "element.".$this->checkNames($column['name'])." + ';' + ";
            }
        }
        foreach ($relationships as $relationship) {
            if ($relationship['kind'] !== 'many2many') {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    $colsHeader .= "". $this->checkNames($relationship['fromSingular']) ."_id;";
                    $colsRows .= "element.". $this->checkNames($relationship['fromSingular']) ."_id + ';' + ";
                }
            }
        }
        $colsHeader = trim($colsHeader,";");
        $colsRows = trim($colsRows," + ';' + ");
        $content .= "         let output = '".$colsHeader."\\n';\n";
        $content .= "         backupData.forEach(element => {\n";
        $content .= "            output += ".$colsRows." + '\\n';\n";
        $content .= "         });\n";
        $content .= "         const blob = new Blob([output], { type: 'text/plain' });\n";
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
        $content .= "            this.".$this->checkNames($tableNameSingular)."DataService.masiveLoad(newData).then( r => {\n";
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
                    $content .= "   select".$relationship['fromSingular']."(".$this->checkNames($relationship['fromSingular']).": ".$relationship['fromSingular'].") {\n";
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = ".$this->checkNames($relationship['fromSingular']).".id;\n";
                    $content .= "   }\n\n";
                    $content .= "   add".$relationship['fromSingular']."() {\n";
                    $content .= "      if (this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId === 0) {\n";
                    $content .= "         this.toastr.errorToastr('Seleccione un registro.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      this.".$this->checkNames($relationship['fromPlural']).".forEach(".$this->checkNames($relationship['fromSingular'])." => {\n";
                    $content .= "         if (".$this->checkNames($relationship['fromSingular']).".id == this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId) {\n";
                    $content .= "            let existe = false;\n";
                    $content .= "            this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular']).".forEach(element => {\n";
                    $content .= "               if (element.id == ".$this->checkNames($relationship['fromSingular']).".id) {\n";
                    $content .= "                  existe = true;\n";
                    $content .= "               }\n";
                    $content .= "            });\n";
                    $content .= "            if (!existe) {\n";
                    $content .= "               this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular']).".push(".$this->checkNames($relationship['fromSingular']).");\n";
                    $content .= "               this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = 0;\n";
                    $content .= "            } else {\n";
                    $content .= "               this.toastr.errorToastr('El registro ya existe.', 'Error');\n";
                    $content .= "            }\n";
                    $content .= "         }\n";
                    $content .= "      });\n";
                    $content .= "   }\n\n";
                    $content .= "   remove".$relationship['fromSingular']."() {\n";
                    $content .= "      if (this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId === 0) {\n";
                    $content .= "         this.toastr.errorToastr('Seleccione un registro.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      const new".$relationship['fromPlural'].": ".$relationship['fromSingular']."[] = [];\n";
                    $content .= "      let eliminado = false;\n";
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular']).".forEach(".$this->checkNames($relationship['fromSingular'])." => {\n";
                    $content .= "         if (".$this->checkNames($relationship['fromSingular']).".id !== this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId) {\n";
                    $content .= "            new".$relationship['fromPlural'].".push(".$this->checkNames($relationship['fromSingular']).");\n";
                    $content .= "         } else {\n";
                    $content .= "            eliminado = true;\n";
                    $content .= "         }\n";
                    $content .= "      });\n";
                    $content .= "      if (!eliminado) {\n";
                    $content .= "         this.toastr.errorToastr('Registro no encontrado.', 'Error');\n";
                    $content .= "         return;\n";
                    $content .= "      }\n";
                    $content .= "      this.".$this->checkNames($tableNameSingular)."Selected.".$this->checkNames($relationship['fromPlural'])."_on_".$this->checkNames($relationship['toSingular'])." = new".$relationship['fromPlural'].";\n";
                    $content .= "      this.".$this->checkNames($relationship['fromPlural'])."_".$this->checkNames($relationship['toSingular'])."SelectedId = 0;\n";
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
            $content .= "            if (typeof this.".$this->checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
            $content .= "               this.".$this->checkNames($tableNameSingular)."DataService.post(this.".$this->checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "                  this.toastr.successToastr('Datos guardados satisfactoriamente.', 'Nuevo');\n";
            $content .= "                  this.get".$tableNamePlural."();\n";
            $content .= "               }).catch( e => console.log(e) );\n";
            $content .= "            } else {\n";
            $content .= "               this.".$this->checkNames($tableNameSingular)."DataService.put(this.".$this->checkNames($tableNameSingular)."Selected).then( r => {\n";
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
            $content .= "      if (typeof this.".$this->checkNames($tableNameSingular)."Selected.id === 'undefined') {\n";
            $content .= "         this.".$this->checkNames($tableNameSingular)."DataService.post(this.".$this->checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "            this.toastr.successToastr('Datos guardados satisfactoriamente.', 'Nuevo');\n";
            $content .= "            this.get".$tableNamePlural."();\n";
            $content .= "         }).catch( e => console.log(e) );\n";
            $content .= "      } else {\n";
            $content .= "         this.".$this->checkNames($tableNameSingular)."DataService.put(this.".$this->checkNames($tableNameSingular)."Selected).then( r => {\n";
            $content .= "            this.toastr.successToastr('Registro actualizado satisfactoriamente.', 'Actualizar');\n";
            $content .= "            this.get".$tableNamePlural."();\n";
            $content .= "         }).catch( e => console.log(e) );\n";
            $content .= "      }\n";
            $content .= "   }\n\n";
            $content .= "   cancelDialogResult() {\n";
            $content .= "      this.showDialog = false;";
            $content .= "      this.goToPage(this.currentPage);\n";
            $content .= "   }\n";
        }
        $content .= "}";
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.ts", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function createModule($args, $type, $moduleName) {
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
                    $content .= "import { ".$relationship['fromSingular']."Service } from './../../../../services/profile/".strtolower($relationship['toSingular']).".service';\n";
                } else {
                    if ($relationship['kind'] === 'many2one') {
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
        }
        $content .= "               ".$tableNameSingular."Service\n";
        $content .= "               ]\n";
        $content .= "})\n";
        $content .= "export class ".$tableNameSingular."Module {}";
        $status = 'Success';
        try{
            $archivo = fopen("./output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".module.ts", "w");
            fwrite($archivo, $content);
            fclose($archivo);
            $status = 'Success';
        }catch (Exception $e) {
            $status = 'Fail';
        }
        return $status;
    }

    private function checkNames($input) {
        $output = '';
        $estadoAnterior = "lower";
        foreach(str_split($input) as $char) {
            if ($char === strtoupper($char)){
                if ($estadoAnterior === "lower") {
                    $estadoAnterior = "upper";
                  $output .= '_';
                }
            } else {
                if ($estadoAnterior === "upper") {
                    $estadoAnterior = "lower";
                }
            }
            $output .= strtolower($char);
        }
        return trim($output,'_');
    }
}
