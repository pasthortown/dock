<?php

namespace App\Http\Controllers;

class ComposerJSONFileTemplateController extends Controller
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

        $content = "{\n";
        $content .= "    \"name\": \"laravel/lumen\",\n";
        $content .= "    \"description\": \"The Laravel Lumen Framework.\",\n";
        $content .= "    \"keywords\": [\"framework\", \"laravel\", \"lumen\"],\n";
        $content .= "    \"license\": \"MIT\",\n";
        $content .= "    \"type\": \"project\",\n";
        $content .= "    \"require\": {\n";
        $content .= "        \"php\": \"^7.2\",\n";
        $content .= "        \"firebase/php-jwt\": \"^5.0\",\n";
        $content .= "        \"illuminate/mail\": \"^6.11\",\n";
        if ($bddType !== "SQL") {
            $content .= "        \"jenssegers/mongodb\": \"^3.6\",\n";
        }
        $content .= "        \"laravel/lumen-framework\": \"^6.0\",\n";
        $content .= "        \"palanik/lumen-cors\": \"dev-master\"\n";
        $content .= "    },\n";
        $content .= "    \"require-dev\": {\n";
        $content .= "        \"fzaninotto/faker\": \"^1.4\",\n";
        $content .= "        \"phpunit/phpunit\": \"^8.0\",\n";
        $content .= "        \"mockery/mockery\": \"^1.0\"\n";
        $content .= "    },\n";
        $content .= "    \"autoload\": {\n";
        $content .= "        \"classmap\": [\n";
        $content .= "            \"database/seeds\",\n";
        $content .= "            \"database/factories\"\n";
        $content .= "        ],\n";
        $content .= "        \"psr-4\": {\n";
        $content .= "            \"App\\\\\": \"app/\"\n";
        $content .= "        }\n";
        $content .= "    },\n";
        $content .= "    \"autoload-dev\": {\n";
        $content .= "        \"classmap\": [\n";
        $content .= "            \"tests/\"\n";
        $content .= "        ]\n";
        $content .= "    },\n";
        $content .= "    \"scripts\": {\n";
        $content .= "        \"post-root-package-install\": [\n";
        $content .= "            \"@php -r \\\"file_exists('.env') || copy('.env.example', '.env');\\\"\"\n";
        $content .= "        ]\n";
        $content .= "    },\n";
        $content .= "    \"config\": {\n";
        $content .= "        \"preferred-install\": \"dist\",\n";
        $content .= "        \"sort-packages\": true,\n";
        $content .= "        \"optimize-autoloader\": true\n";
        $content .= "    },\n";
        $content .= "    \"minimum-stability\": \"dev\",\n";
        $content .= "    \"prefer-stable\": true\n";
        $content .= "}\n";
        return $content;
    }
}
