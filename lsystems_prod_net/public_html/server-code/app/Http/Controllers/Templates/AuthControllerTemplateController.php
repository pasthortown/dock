<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

class AuthControllerTemplateController extends Controller
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
        $content .= "use Validator;\n";
        $content .= "use Exception;\n";
        $content .= "use App\\User;\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "use Firebase\\JWT\\JWT;\n";
        $content .= "use Firebase\\JWT\\ExpiredException;\n";
        $content .= "use Illuminate\\Support\\Facades\\Hash;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n";
        $content .= "use Illuminate\\Support\\Facades\\Mail;\n";
        $content .= "use Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException;\n";
        if ($bddType == "SQL") {
            $content .= "use Illuminate\\Database\\Eloquent\\ModelNotFoundException;\n";
        }
        $content .= "use Illuminate\\Support\\Facades\\Crypt;\n";
        $content .= "use Illuminate\\Support\\Str;\n";
        $content .= "\n";
        $content .= "class AuthController extends Controller\n";
        $content .= "{\n";
        $content .= "  function passwordRecoveryRequest(Request \$data) {\n";
        $content .= "    \$result = \$data->json()->all();\n";
        $content .= "    \$email = \$result['email'];\n";
        $content .= "    \$user = User::where('email', \$email)->first();\n";
        $content .= "    if(!\$user){\n";
        $content .= "      return response()->json('Ocurrió un error',400);\n";
        $content .= "    }\n";
        $content .= "    \$enlace = env('APP_URL').'password_recovery/?r='.\$user->api_token;\n";
        $content .= "    \$message = 'Para cambiar tu contraseña da click en el siguiente enlace: ' . \$enlace;\n";
        $content .= "    \$subject = 'Solicitud de Cambio de Contraseña';\n";
        $content .= "    return \$this->send_mail(\$user->email, \$user->name, \$subject, \$message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  function passwordRecovery(Request \$data)\n";
        $content .= "  {\n";
        $content .= "    \$token = \$data['r'];\n";
        $content .= "    \$credentials = JWT::decode(\$token, env('JWT_SECRET'), ['HS256']);\n";
        $content .= "    try{\n";
        $content .= "      \$new_password = Str::random(10);\n";
        if ($bddType == "SQL") {
            $content .= "      DB::beginTransaction();\n";
            $content .= "      \$status = User::find(\$credentials->subject)->update([\n";
            $content .= "        'password'=>Crypt::encrypt(\$new_password),\n";
            $content .= "      ]);\n";
            $content .= "      DB::commit();\n";
        } else {
            $content .= "      \$status = User::find(\$credentials->subject);\n";
            $content .= "      \$status->password = Crypt::encrypt(\$new_password);\n";
            $content .= "      \$status->save();\n";
        }
        $content .= "      if(!\$status){\n";
        $content .= "        return response()->json('Ocurrió un error',400);\n";
        $content .= "      }\n";
        $content .= "    } catch (Exception \$e) {\n";
        $content .= "      return response()->json('Ocurrió un error',400);\n";
        $content .= "    }\n";
        $content .= "    \$message = 'Tu nueva contraseña es ' . \$new_password;\n";
        $content .= "    \$subject = 'Recuperación de Contraseña';\n";
        $content .= "    \$user = User::where('id', \$credentials->subject)->first();\n";
        $content .= "    return \$this->send_mail(\$user->email, \$user->name, \$subject, \$message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  function passwordChange(Request \$data)\n";
        $content .= "  {\n";
        $content .= "    \$result = \$data->json()->all();\n";
        $content .= "    \$id = \$data->auth->id;\n";
        $content .= "    \$new_password = \$result['new_password'];\n";
        $content .= "    try{\n";
        if ($bddType == "SQL") {
            $content .= "      DB::beginTransaction();\n";
            $content .= "      \$user = User::find(\$id)->update([\n";
            $content .= "        'password'=>Crypt::encrypt(\$new_password),\n";
            $content .= "      ]);\n";
            $content .= "      DB::commit();\n";
        } else {
            $content .= "      \$user = User::find(\$id);\n";
            $content .= "      \$user->password = Crypt::encrypt(\$new_password);\n";
            $content .= "      \$user->save();\n";
        }
        $content .= "    } catch (Exception \$e) {\n";
        $content .= "      return response()->json([\n";
        $content .= "        'error' => 'Bad Credentials'\n";
        $content .= "      ], 400);\n";
        $content .= "    }\n";
        $content .= "    return response()->json('Password changed successfully',200);\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  function register(Request \$data)\n";
        $content .= "  {\n";
        $content .= "    try{\n";
        $content .= "      \$new_password = Str::random(10);\n";
        $content .= "      \$result = \$data->json()->all();\n";
        $content .= "      \$email = \$result['email'];\n";
        if ($bddType == "SQL") {
            $content .= "      DB::beginTransaction();\n";
            $content .= "      \$user = new User();\n";
            $content .= "      \$lastUser = User::orderBy('id')->get()->last();\n";
            $content .= "      if(\$lastUser) {\n";
            $content .= "         \$user->id = \$lastUser->id + 1;\n";
            $content .= "      } else {\n";
            $content .= "         \$user->id = 1;\n";
            $content .= "      }\n";
            $content .= "      \$user->name = \$result['name'];\n";
            $content .= "      \$user->email = \$email;\n";
            $content .= "      \$user->password = Crypt::encrypt(\$new_password);\n";
            $content .= "      \$user->api_token =>Str::random(64);\n";
            $content .= "      \$user->save();\n";
            $content .= "      DB::commit();\n";
        } else {
            $content .= "      \$lastUser = User::orderBy('id', 'desc')->first();\n";
            $content .= "      if(\$lastUser) {\n";
            $content .= "         \$id = \$lastUser->id + 1;\n";
            $content .= "      } else {\n";
            $content .= "         \$id = 1;\n";
            $content .= "      }\n";
            $content .= "      \$user = User::create([\n";
            $content .= "          'id' => \$id,\n";
            $content .= "          'name' => \$result['name'],\n";
            $content .= "          'email' => \$email,\n";
            $content .= "          'password' => Crypt::encrypt(\$new_password),\n";
            $content .= "          'api_token' => Str::random(64),\n";
            $content .= "      ]);\n";
        }
        $content .= "      \$message = 'Tu nueva contraseña es ' . \$new_password;\n";
        $content .= "      \$subject = 'Te damos la bienvenida a ' . env('MAIL_FROM_NAME');\n";
        $content .= "      return \$this->send_mail(\$email, \$user->name, \$subject, \$message, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));\n";
        $content .= "    } catch (Exception \$e) {\n";
        $content .= "      return response()->json(\$e,400);\n";
        $content .= "    }\n";
        $content .= "    return response()->json(\$user,200);\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  function login(Request \$data)\n";
        $content .= "  {\n";
        $content .= "      \$result = \$data->json()->all();\n";
        $content .= "      \$email = \$result['email'];\n";
        $content .= "      \$password = \$result['password'];\n";
        $content .= "      \$user = User::where('email', \$email)->first();\n";
        $content .= "      if (!\$user) {\n";
        $content .= "        return response()->json([\n";
        $content .= "          'error' => 'Bad Credentials'\n";
        $content .= "        ], 400);\n";
        $content .= "      }\n";
        $content .= "      if (\$password === Crypt::decrypt(\$user->password)) {\n";
        $content .= "        \$token = \$this->jwt(\$user);\n";
        $content .= "        \$user->api_token = \$token;\n";
        if ($bddType == "SQL") {
            $content .= "        \$response = User::where('id',\$user->id)->update([\n";
            $content .= "          'api_token'=>\$token,\n";
            $content .= "        ]);\n";
        } else {
            $content .= "        \$user->save();\n";
        }
        $content .= "        return response()->json([\n";
        $content .= "            'token' => \$token,\n";
        $content .= "            'name' => \$user->name,\n";
        $content .= "            'id' => \$user->id\n";
        $content .= "        ], 200);\n";
        $content .= "      }\n";
        $content .= "      return response()->json([\n";
        $content .= "        'error' => 'Bad Credentials'\n";
        $content .= "      ], 400);\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  protected function jwt(User \$user) {\n";
        $content .= "    \$payload = [\n";
        $content .= "        'subject' => \$user->id,\n";
        $content .= "        'creation_time' => time(),\n";
        $content .= "        'expiration_time' => time() + 60*60\n";
        $content .= "    ];\n";
        $content .= "    return JWT::encode(\$payload, env('JWT_SECRET'));\n";
        $content .= "  }\n";
        $content .= "\n";
        $content .= "  protected function send_mail(\$to, \$toAlias, \$subject, \$body, \$fromMail,\$fromAlias) {\n";
        $content .= "    \$data = ['name'=>\$toAlias, 'body'=>\$body, 'appName'=>env('MAIL_FROM_NAME')];\n";
        $content .= "    Mail::send('mail', \$data, function(\$message) use (\$to, \$toAlias, \$subject, \$fromMail,\$fromAlias) {\n";
        $content .= "      \$message->to(\$to, \$toAlias)->subject(\$subject);\n";
        $content .= "      \$message->from(\$fromMail,\$fromAlias);\n";
        $content .= "    });\n";
        $content .= "    return response()->json('Success!',200);\n";
        $content .= "  }\n";
        $content .= "}\n";
        return $content;
    }
}
