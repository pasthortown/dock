<?php

namespace App\Http\Controllers;
use App\Http\Controllers\UtilitiesController;

class ControllerTemplateController extends Controller
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
        $migrationIndex = $args['MigrationIndex'];
        $colsAsText = "";
        $colsAsTextNoHidden = "";
        $colsHidden = [];
        $colsVisibles = [];
        foreach ($tableColumns as $column) {
            if ($column['group']!=="hidden") {
                if ($column['type']==="gmap") {
                    array_push($colsVisibles,UtilitiesController::checkNames($column['name'])."_latitude");
                    array_push($colsVisibles,UtilitiesController::checkNames($column['name'])."_longitude");
                } else {
                    array_push($colsVisibles,UtilitiesController::checkNames($column['name']));
                }
            } else {
                if ($column['type']==="gmap") {
                    array_push($colsHidden,UtilitiesController::checkNames($column['name'])."_latitude");
                    array_push($colsHidden,UtilitiesController::checkNames($column['name'])."_longitude");
                } else {
                    array_push($colsHidden,UtilitiesController::checkNames($column['name']));
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
                    $colsAsText .= "          \$". strtolower($tableNameSingular) ."->".UtilitiesController::checkNames($relationship['fromSingular'])."_id = \$result['".UtilitiesController::checkNames($relationship['fromSingular'])."_id'];\n";
                    $colsAsTextNoHidden .= "             '".UtilitiesController::checkNames($relationship['fromSingular'])."_id'=>\$result['".UtilitiesController::checkNames($relationship['fromSingular'])."_id'],\n";
                }
            }
            if ($relationship['fromSingular'] === $tableNameSingular) {
                if ($relationship['kind'] === 'many2one') {
                    $colsAsText .= "          \$". strtolower($tableNameSingular) ."->".UtilitiesController::checkNames($relationship['toSingular'])."_id = \$result['".UtilitiesController::checkNames($relationship['toSingular'])."_id'];\n";
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
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\ModelNotFoundException;\n\n";
        } else {
            $content .= "use Illuminate\\Pagination\\Paginator;\n";
            $content .= "use Illuminate\\Pagination\\LengthAwarePaginator;\n\n";
        }
        $content .= "class ". $tableNameSingular ."Controller extends Controller\n{\n";
        $content .= "    function get(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$id = \$data['id'];\n";
        $content .= "       if (\$id == null) {\n";
        if ($bddType == "SQL") {
            $content .= "          return response()->json(". $tableNameSingular ."::get(),200);\n";
        } else {
            $content .= "          return response()->json(". $tableNameSingular ."::all(),200);\n";
        }
        $content .= "       } else {\n";
        if ($bddType == "SQL") {
            $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::findOrFail(\$id);\n";
            $content .= "          \$attach = [];\n";
            foreach ($relationships as $relationship) {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "          \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                        $content .= "          array_push(\$attach, [\"".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."\"=>\$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."]);\n";
                    }
                }
            }
            $content .= "          return response()->json([\"". $tableNameSingular ."\"=>\$". strtolower($tableNameSingular) .", \"attach\"=>\$attach],200);\n";
        } else {
            $content .= "          return response()->json(". $tableNameSingular ."::find(intval(\$id)),200);\n";
        }
        $content .= "       }\n";
        $content .= "    }\n\n";
        $content .= "    function paginate(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$size = \$data['size'];\n";
        if ($bddType == "SQL") {
            $content .= "       return response()->json(". $tableNameSingular ."::paginate(\$size),200);\n";
        } else {
            $content .= "       \$currentPage = \$data->input('page', 1);\n";
            $content .= "       \$offset = (\$currentPage - 1) * \$size;\n";
            $content .= "       \$total = ". $tableNameSingular ."::count();\n";
            $content .= "       \$result = ". $tableNameSingular ."::offset(\$offset)->limit(intval(\$size))->get();\n";
            $content .= "       \$toReturn = new LengthAwarePaginator(\$result, \$total, \$size, \$currentPage, [\n";
            $content .= "          'path' => Paginator::resolveCurrentPath(),\n";
            $content .= "          'pageName' => 'page'\n";
            $content .= "       ]);\n";
            $content .= "       return response()->json(\$toReturn,200);\n";
        }
        $content .= "    }\n\n";
        $content .= "    function post(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          \$result = \$data->json()->all();\n";
        if ($bddType == "SQL") {
            $content .= "          DB::beginTransaction();\n";
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
                        $content .= "          \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = \$result['".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."'];\n";
                        $content .= "          foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as \$".UtilitiesController::checkNames($relationship['fromSingular']).") {\n";
                        $content .= "             \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".UtilitiesController::checkNames($relationship['fromSingular'])."['id']);\n";
                        $content .= "          }\n";
                    }
                }
            }
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$last". $tableNameSingular ." = ". $tableNameSingular ."::orderBy('id', 'desc')->first();\n";
            $content .= "          if(\$last". $tableNameSingular .") {\n";
            $content .= "             \$id = \$last". $tableNameSingular ."->id + 1;\n";
            $content .= "          } else {\n";
            $content .= "             \$id = 1;\n";
            $content .= "          }\n";
            $content .= "          \$". strtolower($tableNameSingular) ." = " . $tableNameSingular ."::create([\n";
            $content .= "             'id' => \$id,\n";
            $content .= $colsAsTextNoHidden;
            $content .= "          ]);\n";
        }
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "       return response()->json(\$". strtolower($tableNameSingular) .",200);\n";
        $content .= "    }\n\n";
        $content .= "    function put(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          \$result = \$data->json()->all();\n";
        if ($bddType == "SQL") {
            $content .= "          DB::beginTransaction();\n";
            $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::where('id',\$result['id'])->update([\n";
            $content .= $colsAsTextNoHidden;
            $content .= "          ]);\n";
            foreach ($relationships as $relationship) {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::where('id',\$result['id'])->first();\n";
                        $content .= "          \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = \$result['".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."'];\n";
                        $content .= "          \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                        $content .= "          foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old as \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old ) {\n";
                        $content .= "             \$delete = true;\n";
                        $content .= "             foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as \$".UtilitiesController::checkNames($relationship['fromSingular'])." ) {\n";
                        $content .= "                if ( \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id === \$".UtilitiesController::checkNames($relationship['fromSingular'])."['id'] ) {\n";
                        $content .= "                   \$delete = false;\n";
                        $content .= "                }\n";
                        $content .= "             }\n";
                        $content .= "             if ( \$delete ) {\n";
                        $content .= "                \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->detach(\$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id);\n";
                        $content .= "             }\n";
                        $content .= "          }\n";
                        $content .= "          foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as \$".UtilitiesController::checkNames($relationship['fromSingular'])." ) {\n";
                        $content .= "             \$add = true;\n";
                        $content .= "             foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old as \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old) {\n";
                        $content .= "                if ( \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id === \$".UtilitiesController::checkNames($relationship['fromSingular'])."['id'] ) {\n";
                        $content .= "                   \$add = false;\n";
                        $content .= "                }\n";
                        $content .= "             }\n";
                        $content .= "             if ( \$add ) {\n";
                        $content .= "                \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".UtilitiesController::checkNames($relationship['fromSingular'])."['id']);\n";
                        $content .= "             }\n";
                        $content .= "          }\n";
                    }
                }
            }
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::find(intval(\$result['id']));\n";
            $content .= $colsAsText;
            $content .= "          \$". strtolower($tableNameSingular) ."->save();\n";
        }
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "       return response()->json(\$". strtolower($tableNameSingular) .",200);\n";
        $content .= "    }\n\n";
        $content .= "    function delete(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$id = \$data['id'];\n";
        if ($bddType == "SQL") {
            $content .= "       return response()->json(". $tableNameSingular ."::destroy(\$id),200);\n";
        } else {
            $content .= "       \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::find(intval(\$id));\n";
            $content .= "       return response()->json(\$". strtolower($tableNameSingular) ."->delete(),200);\n";
        }
        $content .= "    }\n\n";
        $content .= "    function backup(Request \$data)\n";
        $content .= "    {\n";
        if ($bddType == "SQL") {
            $content .= "       \$". strtolower($tableNamePlural) ." = ". $tableNameSingular ."::get();\n";
            $content .= "       \$toReturn = [];\n";
            $content .= "       foreach( \$". strtolower($tableNamePlural) ." as \$". strtolower($tableNameSingular) .") {\n";
            $content .= "          \$attach = [];\n";
            foreach ($relationships as $relationship) {
                if ($relationship['toSingular'] === $tableNameSingular) {
                    if ($relationship['kind'] === 'many2many') {
                        $content .= "          \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                        $content .= "          array_push(\$attach, [\"".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."\"=>\$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."]);\n";
                    }
                }
            }
            $content .= "          array_push(\$toReturn, [\"". $tableNameSingular ."\"=>\$". strtolower($tableNameSingular) .", \"attach\"=>\$attach]);\n";
            $content .= "       }\n";
        } else {
            $content .= "       \$toReturn = ". $tableNameSingular ."::all();\n";
        }
        $content .= "       return response()->json(\$toReturn,200);\n";
        $content .= "    }\n\n";
        $content .= "    function masiveLoad(Request \$data)\n";
        $content .= "    {\n";
        $content .= "      \$incomming = \$data->json()->all();\n";
        $content .= "      \$masiveData = \$incomming['data'];\n";
        if ($bddType == "SQL") {
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
                        $content .= "         \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = [];\n";
                        $content .= "         foreach(\$row['attach'] as \$attach){\n";
                        $content .= "            \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." = \$attach['".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."'];\n";
                        $content .= "         }\n";
                        $content .= "         \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old = \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->get();\n";
                        $content .= "         foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old as \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old ) {\n";
                        $content .= "            \$delete = true;\n";
                        $content .= "            foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as \$".UtilitiesController::checkNames($relationship['fromSingular'])." ) {\n";
                        $content .= "               if ( \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id === \$".UtilitiesController::checkNames($relationship['fromSingular'])."['id'] ) {\n";
                        $content .= "                  \$delete = false;\n";
                        $content .= "               }\n";
                        $content .= "            }\n";
                        $content .= "            if ( \$delete ) {\n";
                        $content .= "               \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->detach(\$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id);\n";
                        $content .= "            }\n";
                        $content .= "         }\n";
                        $content .= "         foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])." as \$".UtilitiesController::checkNames($relationship['fromSingular'])." ) {\n";
                        $content .= "            \$add = true;\n";
                        $content .= "            foreach( \$".UtilitiesController::checkNames($relationship['fromPlural'])."_on_".UtilitiesController::checkNames($relationship['toSingular'])."_old as \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old) {\n";
                        $content .= "               if ( \$".UtilitiesController::checkNames($relationship['fromSingular'])."_old->id === \$".UtilitiesController::checkNames($relationship['fromSingular'])."['id'] ) {\n";
                        $content .= "                  \$add = false;\n";
                        $content .= "               }\n";
                        $content .= "            }\n";
                        $content .= "            if ( \$add ) {\n";
                        $content .= "               \$". strtolower($tableNameSingular) ."->".$relationship['fromPlural']."()->attach(\$".UtilitiesController::checkNames($relationship['fromSingular'])."['id']);\n";
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
        } else {
            $content .= "       foreach(\$masiveData as \$result) {\n";
            $content .= "         \$exist = ". $tableNameSingular ."::where('id',\$result['id'])->first();\n";
            $content .= "         if (\$exist) {\n";
            $content .= "          \$". strtolower($tableNameSingular) ." = ". $tableNameSingular ."::find(intval(\$result['id']));\n";
            $content .= $colsAsText;
            $content .= "          \$". strtolower($tableNameSingular) ."->save();\n";
            $content .= "         } else {\n";
            $content .= "          \$". strtolower($tableNameSingular) ." = " . $tableNameSingular ."::create([\n";
            $content .= "             'id' => \$result['id'],\n";
            $content .= $colsAsTextNoHidden;
            $content .= "          ]);\n";
            $content .= "         }\n";
            $content .= "       }\n";
        }
        $content .= "    }\n";
        $content .= "}";
    return ["Table"=>$args['Table'], "MigrationIndex"=>$migrationIndex , "Content"=>$content];
    }
}
