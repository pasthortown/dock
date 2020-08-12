<?php

namespace App\Http\Controllers;

class ProfilePictureControllerTemplateController extends Controller
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
        $content .= "namespace App\\Http\\Controllers;\n";
        $content .= "\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "Use Exception;\n";
        $content .= "use App\\ProfilePicture;\n";
        $content .= "use App\\User;\n";
        $content .= "use Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\ModelNotFoundException;\n\n";
        } else {
            $content .= "use Illuminate\\Pagination\\Paginator;\n";
            $content .= "use Illuminate\\Pagination\\LengthAwarePaginator;\n\n";
        }
        $content .= "class ProfilePictureController extends Controller\n";
        $content .= "{\n";
        $content .= "    function get(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$user_id = \$data['user_id'];\n";
        if ($bddType == "SQL") {
            $content .= "       \$profilepicture = ProfilePicture::where('id_user', \$user_id)->first();\n";
        } else {
            $content .= "       \$profilepicture = ProfilePicture::where('id_user', intval(\$user_id))->first();\n";
        }
        $content .= "       if (\$profilepicture) {\n";
        $content .= "         return response()->json(\$profilepicture,200);\n";
        $content .= "       }else {\n";
        $content .= "         return response()->json(['id'=>0, 'file_type'=>'', 'file_name'=>'', 'file'=>''],200);\n";
        $content .= "       }\n";
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    function paginate(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$size = \$data['size'];\n";
        if ($bddType == "SQL") {
            $content .= "          return response()->json(ProfilePicture::paginate(\$size),200);\n";
        } else {
            $content .= "       \$currentPage = \$data->input('page', 1);\n";
            $content .= "       \$offset = (\$currentPage - 1) * \$size;\n";
            $content .= "       \$total = ProfilePicture::count();\n";
            $content .= "       \$result = ProfilePicture::offset(\$offset)->limit(intval(\$size))->get();\n";
            $content .= "       \$toReturn = new LengthAwarePaginator(\$result, \$total, \$size, \$currentPage, [\n";
            $content .= "          'path' => Paginator::resolveCurrentPath(),\n";
            $content .= "          'pageName' => 'page'\n";
            $content .= "       ]);\n";
            $content .= "       return response()->json(\$toReturn,200);\n";
        }
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    function post(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          \$result = \$data->json()->all();\n";
        if ($bddType == "SQL") {
            $content .= "          DB::beginTransaction();\n";
            $content .= "          \$profilepicture = new ProfilePicture();\n";
            $content .= "          \$profilepicture->file_type = \$result['file_type'];\n";
            $content .= "          \$profilepicture->file_name = \$result['file_name'];\n";
            $content .= "          \$profilepicture->file = \$result['file'];\n";
            $content .= "          \$profilepicture->id_user = \$data->auth->id;\n";
            $content .= "          \$profilepicture->save();\n";
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$lastProfilePicture = ProfilePicture::orderBy('id', 'desc')->first();\n";
            $content .= "          if(\$lastProfilePicture) {\n";
            $content .= "             \$id = \$lastProfilePicture->id + 1;\n";
            $content .= "          } else {\n";
            $content .= "             \$id = 1;\n";
            $content .= "          }\n";
            $content .= "          \$profilePicture = ProfilePicture::create([\n";
            $content .= "             'id' => \$id,\n";
            $content .= "             'id_user' => \$data->auth->id,\n";
            $content .= "             'file_type'=>\$result['file_type'],\n";
            $content .= "             'file_name'=>\$result['file_name'],\n";
            $content .= "             'file'=>\$result['file'],\n";
            $content .= "          ]);\n";
        }
        $content .= "          return response()->json(\$profilepicture,200);\n";
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    function put(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       try{\n";
        $content .= "          \$result = \$data->json()->all();\n";
        if ($bddType == "SQL") {
            $content .= "          DB::beginTransaction();\n";
            $content .= "          \$profilepicture = ProfilePicture::where('id',\$result['id'])->update([\n";
            $content .= "             'file_type'=>\$result['file_type'],\n";
            $content .= "             'file_name'=>\$result['file_name'],\n";
            $content .= "             'file'=>\$result['file'],\n";
            $content .= "          ]);\n";
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$profilepicture = ProfilePicture::find(intval(\$result['id']));\n";
            $content .= "          \$profilepicture->file_type = \$result['file_type'];\n";
            $content .= "          \$profilepicture->file_name = \$result['file_name'];\n";
            $content .= "          \$profilepicture->file = \$result['file'];\n";
            $content .= "          \$profilepicture->save();\n";
        }
        $content .= "          return response()->json(\$profilepicture,200);\n";
        $content .= "       } catch (Exception \$e) {\n";
        $content .= "          return response()->json(\$e,400);\n";
        $content .= "       }\n";
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    function delete(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$result = \$data->json()->all();\n";
        $content .= "       \$id = \$result['id'];\n";
        if ($bddType == "SQL") {
            $content .= "       return response()->json(ProfilePicture::destroy(\$id), 200);\n";
        } else {
            $content .= "       \$profilePicture = ProfilePicture::find(intval(\$id));\n";
            $content .= "       return response()->json(\$profilePicture->delete(),200);\n";
        }
        $content .= "    }\n";
        $content .= "}\n";
        return $content;
    }
}
