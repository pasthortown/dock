<?php

namespace App\Http\Controllers;

class RouterFileTemplateController extends Controller
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
}
