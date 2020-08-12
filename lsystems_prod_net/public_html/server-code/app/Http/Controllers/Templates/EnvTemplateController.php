<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

class EnvTemplateController extends Controller
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
        $projectName = $args['moduleName'];
        $bddType = $args['bddType'];
        $content = "APP_NAME=\"".$projectName."\"\n";
        $content .= "APP_ENV=local\n";
        $content .= "APP_KEY=".Str::random(32)."\n";
        $content .= "APP_DEBUG=true\n";
        $content .= "APP_URL=http://localhost:8000/\n";
        $content .= "APP_TIMEZONE=America/Bogota\n\n";
        $content .= "LOG_CHANNEL=stack\n";
        $content .= "LOG_SLACK_WEBHOOK_URL=\n\n";
        if ($bddType == "SQL") {
            $content .= "DB_CONNECTION=pgsql\n";
            $content .= "DB_HOST=127.0.0.1\n";
            $content .= "DB_PORT=5432\n";
        } else {
            $content .= "DB_CONNECTION=mongodb\n";
            $content .= "DB_HOST=127.0.0.1\n";
            $content .= "DB_PORT=27017\n";
            $content .= "DB_AUTHENTICATION_DATABASE=admin\n";
        }
        $content .= "DB_DATABASE=databaseName\n";
        $content .= "DB_USERNAME=dbUserName\n";
        $content .= "DB_PASSWORD=dbPassword\n\n";
        $content .= "CACHE_DRIVER=file\n";
        $content .= "QUEUE_CONNECTION=sync\n\n";
        $content .= "JWT_SECRET=".Str::random(32)."\n\n";
        $content .= "MAIL_DRIVER=smtp\n";
        $content .= "MAIL_HOST=smtp.gmail.com\n";
        $content .= "MAIL_PORT=587\n";
        $content .= "MAIL_USERNAME=yourMail@gmail.com\n";
        $content .= "MAIL_PASSWORD=yourMailPassword\n";
        $content .= "MAIL_ENCRYPTION=tls\n";
        $content .= "MAIL_FROM_ADDRESS=yourMail@gmail.com\n";
        $content .= "MAIL_FROM_NAME=\"".$projectName."\"\n";
        return $content;
    }
}
