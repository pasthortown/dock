<?php

namespace App\Http\Controllers;

class UserControllerTemplateController extends Controller
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
        $content .= "use App\\User;\n";
        $content .= "use Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n";
        $content .= "use Illuminate\\Support\\Str;\n";
        $content .= "use Illuminate\\Support\\Facades\\Crypt;\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\ModelNotFoundException;\n\n";
        } else {
            $content .= "use Illuminate\\Pagination\\Paginator;\n";
            $content .= "use Illuminate\\Pagination\\LengthAwarePaginator;\n\n";
        }
        $content .= "class UserController extends Controller\n";
        $content .= "{\n";
        $content .= "    function get(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$id = \$data['id'];\n";
        $content .= "       if (\$id == null) {\n";
        if ($bddType == "SQL") {
            $content .= "          return response()->json(User::get(),200);\n";
        } else {
            $content .= "          return response()->json(User::all(),200);\n";
        }
        $content .= "       } else {\n";
        if ($bddType == "SQL") {
            $content .= "          return response()->json(User::findOrFail(\$id),200);\n";
        } else {
            $content .= "          return response()->json(User::find(intval(\$id)),200);\n";
        }
        $content .= "       }\n";
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    function paginate(Request \$data)\n";
        $content .= "    {\n";
        $content .= "       \$size = \$data['size'];\n";
        if ($bddType == "SQL") {
            $content .= "          return response()->json(User::paginate(\$size),200);\n";
        } else {
            $content .= "       \$currentPage = \$data->input('page', 1);\n";
            $content .= "       \$offset = (\$currentPage - 1) * \$size;\n";
            $content .= "       \$total = User::count();\n";
            $content .= "       \$result = User::offset(\$offset)->limit(intval(\$size))->get();\n";
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
            $content .= "          \$user = new User();\n";
            $content .= "          \$user->name = \$result['name'];\n";
            $content .= "          \$user->email = \$result['email'];\n";
            $content .= "          \$user->password = Crypt::encrypt(Str::random(32));\n";
            $content .= "          \$user->api_token = Str::random(32);\n";
            $content .= "          \$user->save();\n";
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$lastUser = User::orderBy('id', 'desc')->first();\n";
            $content .= "          if(\$lastUser) {\n";
            $content .= "             \$id = \$lastUser->id + 1;\n";
            $content .= "          } else {\n";
            $content .= "             \$id = 1;\n";
            $content .= "          }\n";
            $content .= "          \$user = User::create([\n";
            $content .= "             'id' => \$id,\n";
            $content .= "             'name'=>\$result['name'],\n";
            $content .= "             'email'=>\$result['email'],\n";
            $content .= "             'password'=>Crypt::encrypt(Str::random(32)),\n";
            $content .= "             'api_token'=>Str::random(32),\n";
            $content .= "          ]);\n";
        }
        $content .= "          return response()->json(\$user,200);\n";
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
            $content .= "          \$user = User::where('id',\$result['id'])->update([\n";
            $content .= "             'name'=>\$result['name'],\n";
            $content .= "             'email'=>\$result['email'],\n";
            $content .= "          ]);\n";
            $content .= "          DB::commit();\n";
        } else {
            $content .= "          \$user = User::find(intval(\$result['id']));\n";
            $content .= "          \$user->name = \$result['name'];\n";
            $content .= "          \$user->email = \$result['email'];\n";
            $content .= "          \$user->save();\n";
        }
        $content .= "          return response()->json(\$user,200);\n";
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
            $content .= "       return response()->json(User::destroy(\$id), 200);\n";
        } else {
            $content .= "       \$user = User::find(intval(\$id));\n";
            $content .= "       return response()->json(\$user->delete(),200);\n";
        }
        $content .= "    }\n";
        $content .= "}\n";
        return $content;
    }
}
