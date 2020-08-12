<?php

namespace App\Http\Controllers;

class AppBootstrapFileTemplateController extends Controller
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
        $content .= "require_once __DIR__.'/../vendor/autoload.php';\n";
        $content .= "\n";
        $content .= "(new Laravel\\Lumen\\Bootstrap\\LoadEnvironmentVariables(\n";
        $content .= "    dirname(__DIR__)\n";
        $content .= "))->bootstrap();\n";
        $content .= "\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Create The Application\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Here we will load the environment and create the application instance\n";
        $content .= "| that serves as the central piece of this framework. We'll use this\n";
        $content .= "| application as an \"IoC\" container and router for this framework.\n";
        $content .= "|\n";
        $content .= "*/\n";
        $content .= "\n";
        $content .= "\$app = new Laravel\\Lumen\\Application(\n";
        $content .= "    dirname(__DIR__)\n";
        $content .= ");\n";
        $content .= "\n";
        $content .= "\$app->withFacades();\n";
        if ($bddType !== "SQL") {
            $content .= "\$app->register(Jenssegers\Mongodb\MongodbServiceProvider::class);\n";
        }
        $content .= "\$app->withEloquent();\n";
        $content .= "\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Register Container Bindings\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Now we will register a few bindings in the service container. We will\n";
        $content .= "| register the exception handler and the console kernel. You may add\n";
        $content .= "| your own bindings here if you like or you can make another file.\n";
        $content .= "|\n";
        $content .= "*/\n";
        $content .= "\n";
        $content .= "\$app->singleton(\n";
        $content .= "    Illuminate\\Contracts\\Debug\\ExceptionHandler::class,\n";
        $content .= "    App\\Exceptions\\Handler::class\n";
        $content .= ");\n";
        $content .= "\n";
        $content .= "\$app->singleton(\n";
        $content .= "    Illuminate\\Contracts\\Console\\Kernel::class,\n";
        $content .= "    App\\Console\\Kernel::class\n";
        $content .= ");\n";
        $content .= "\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Register Middleware\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Next, we will register the middleware with the application. These can\n";
        $content .= "| be global middleware that run before and after each request into a\n";
        $content .= "| route or middleware that'll be assigned to some specific routes.\n";
        $content .= "|\n";
        $content .= "*/\n";
        $content .= "\n";
        $content .= "\$app->middleware([\n";
        $content .= "    'palanik\\lumen\\Middleware\\LumenCors'\n";
        $content .= "]);\n";
        $content .= "\n";
        $content .= "\$app->routeMiddleware([\n";
        $content .= "     'auth' => App\\Http\\Middleware\\Authenticate::class,\n";
        $content .= "]);\n";
        $content .= "\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Register Service Providers\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Here we will register all of the application's service providers which\n";
        $content .= "| are used to bind services into the container. Service providers are\n";
        $content .= "| totally optional, so you are not required to uncomment this line.\n";
        $content .= "|\n";
        $content .= "*/\n";
        $content .= "\n";
        $content .= "// \$app->register(App\\Providers\\AppServiceProvider::class);\n";
        $content .= "\$app->register(App\\Providers\\AuthServiceProvider::class);\n";
        $content .= "\$app->register(Illuminate\\Mail\\MailServiceProvider::class);\n";
        $content .= "// \$app->register(App\\Providers\\EventServiceProvider::class);\n";
        $content .= "\n";
        $content .= "/*\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "| Load The Application Routes\n";
        $content .= "|--------------------------------------------------------------------------\n";
        $content .= "|\n";
        $content .= "| Next we will include the routes file so that they can all be added to\n";
        $content .= "| the application. This will provide all of the URLs the application\n";
        $content .= "| can respond to, as well as the controllers that may handle them.\n";
        $content .= "|\n";
        $content .= "*/\n";
        $content .= "\n";
        $content .= "\$app->configure('mail');\n";
        $content .= "\$app->alias('mailer', Illuminate\\Mail\\Mailer::class);\n";
        $content .= "\$app->alias('mailer', Illuminate\\Contracts\\Mail\\Mailer::class);\n";
        $content .= "\$app->alias('mailer', Illuminate\\Contracts\\Mail\\MailQueue::class);\n";
        $content .= "\n";
        $content .= "\$app->router->group([\n";
        $content .= "    'namespace' => 'App\\Http\\Controllers',\n";
        $content .= "], function (\$router) {\n";
        $content .= "    require __DIR__.'/../routes/web.php';\n";
        $content .= "});\n";
        $content .= "\n";
        $content .= "return \$app;\n";
        return $content;
    }
}
