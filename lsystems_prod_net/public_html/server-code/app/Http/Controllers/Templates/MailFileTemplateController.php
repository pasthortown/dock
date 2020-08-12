<?php

namespace App\Http\Controllers;

class MailFileTemplateController extends Controller
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
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Mail Driver\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | Laravel supports both SMTP and PHP's \"mail\" function as drivers for the\n";
        $content .= "    | sending of e-mail. You may specify which one you're using throughout\n";
        $content .= "    | your application here. By default, Laravel is setup for SMTP mail.\n";
        $content .= "    |\n";
        $content .= "    | Supported: \"smtp\", \"sendmail\", \"mailgun\", \"mandrill\", \"ses\",\n";
        $content .= "    |            \"sparkpost\", \"log\", \"array\"\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'driver' => env('MAIL_DRIVER', 'smtp'),\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | SMTP Host Address\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | Here you may provide the host address of the SMTP server used by your\n";
        $content .= "    | applications. A default option is provided that is compatible with\n";
        $content .= "    | the Mailgun mail service which will provide reliable deliveries.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | SMTP Host Port\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | This is the SMTP port used by your application to deliver e-mails to\n";
        $content .= "    | users of the application. Like the host we have set this value to\n";
        $content .= "    | stay compatible with the Mailgun e-mail application by default.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'port' => env('MAIL_PORT', 587),\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Global \"From\" Address\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | You may wish for all e-mails sent by your application to be sent from\n";
        $content .= "    | the same address. Here, you may specify a name and address that is\n";
        $content .= "    | used globally for all e-mails that are sent by your application.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'from' => [\n";
        $content .= "        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),\n";
        $content .= "        'name' => env('MAIL_FROM_NAME', 'Example'),\n";
        $content .= "    ],\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | E-Mail Encryption Protocol\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | Here you may specify the encryption protocol that should be used when\n";
        $content .= "    | the application send e-mail messages. A sensible default using the\n";
        $content .= "    | transport layer security protocol should provide great security.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'encryption' => env('MAIL_ENCRYPTION', 'tls'),\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | SMTP Server Username\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | If your SMTP server requires a username for authentication, you should\n";
        $content .= "    | set it here. This will get used to authenticate with your server on\n";
        $content .= "    | connection. You may also set the \"password\" value below this one.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'username' => env('MAIL_USERNAME'),\n";
        $content .= "    'password' => env('MAIL_PASSWORD'),\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Sendmail System Path\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | When using the \"sendmail\" driver to send e-mails, we will need to know\n";
        $content .= "    | the path to where Sendmail lives on this server. A default path has\n";
        $content .= "    | been provided here, which will work well on most of your systems.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'sendmail' => '/usr/sbin/sendmail -bs',\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Markdown Mail Settings\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | If you are using Markdown based email rendering, you may configure your\n";
        $content .= "    | theme and component paths here, allowing you to customize the design\n";
        $content .= "    | of the emails. Or, you may simply stick with the Laravel defaults!\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'markdown' => [\n";
        $content .= "        'theme' => 'default',\n";
        $content .= "        'paths' => [\n";
        $content .= "            resource_path('views/vendor/mail'),\n";
        $content .= "        ],\n";
        $content .= "    ],\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Log Channel\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | If you are using the \"log\" driver, you may specify the logging channel\n";
        $content .= "    | if you prefer to keep mail messages separate from other log entries\n";
        $content .= "    | for simpler reading. Otherwise, the default channel will be used.\n";
        $content .= "    |\n";
        $content .= "    */\n";
        $content .= "    'log_channel' => env('MAIL_LOG_CHANNEL'),\n";
        $content .= "];\n";
        return $content;
    }
}
