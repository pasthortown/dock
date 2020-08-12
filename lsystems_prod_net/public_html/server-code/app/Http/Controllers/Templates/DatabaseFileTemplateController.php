<?php

namespace App\Http\Controllers;

class DatabaseFileTemplateController extends Controller
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
        $content = "<?php\n";
        $content .= "return [\n";
        $content .= "    'default' => 'mongodb',\n";
        $content .= "    'connections' => [\n";
        $content .= "        'mongodb' => [\n";
        $content .= "            'driver' => 'mongodb',\n";
        $content .= "            'host' => env('DB_HOST', 'localhost'),\n";
        $content .= "            'port' => env('DB_PORT', 27017),\n";
        $content .= "            'database' => env('DB_DATABASE'),\n";
        $content .= "            'username' => env('DB_USERNAME'),\n";
        $content .= "            'password' => env('DB_PASSWORD'),\n";
        $content .= "            'options' => [\n";
        $content .= "                'database' => 'admin' // sets the authentication database required by mongo 3\n";
        $content .= "            ]\n";
        $content .= "        ],\n";
        $content .= "    ],\n";
        $content .= "    'migrations' => 'migrations',\n";
        $content .= "];\n";
        $content .= "\n";
        return $content;
    }
}
