<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Http\Controllers\FileContentBuilderController;
use App\Http\Controllers\UtilitiesController;

use App\Http\Controllers\AuthTemplateController;
use App\Http\Controllers\BodyComponentHTMLTemplateController;
use App\Http\Controllers\ComponentSpecTemplateController;
use App\Http\Controllers\ControllerTemplateController;
use App\Http\Controllers\EnvironmentsTemplateController;
use App\Http\Controllers\LayoutRoutingModuleTemplateController;
use App\Http\Controllers\MigrationTemplateController;
use App\Http\Controllers\ModelTemplateController;
use App\Http\Controllers\ModelTSTemplateController;
use App\Http\Controllers\ModuleSpecTemplateController;
use App\Http\Controllers\ModuleTemplateController;
use App\Http\Controllers\ProfilePictureServiceTemplateController;
use App\Http\Controllers\RelationshipMany2ManyTemplateController;
use App\Http\Controllers\RouterFileTemplateController;
use App\Http\Controllers\RoutingModuleTemplateController;
use App\Http\Controllers\SCSSComponentTemplateController;
use App\Http\Controllers\ServiceTSTemplateController;
use App\Http\Controllers\SideBarTemplateController;
use App\Http\Controllers\UserServiceTemplateController;
use App\Http\Controllers\EnvTemplateController;

class FileConstructorController extends Controller
{
    static function saveRoutingModuleOf($args, $type, $moduleName) {
        $content = RoutingModuleTemplateController::build($args);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular)."-routing.module.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveComponentSCSSOf($args, $type, $moduleName) {
        $content = SCSSComponentTemplateController::build($args, $type);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.scss";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveEnvFile($type, $args) {
        $content = EnvTemplateController::build($args);
        $file_name = "output-".$type."/server/.env";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveAuthControllerFile($type, $args) {
        $content = AuthControllerTemplateController::build($args);
        $file_name = "output-".$type."/server/app/Http/Controllers/AuthController.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveUserMigrationFile($type, $args) {
        $content = UserMigrationTemplateController::build($args);
        $file_name = "output-".$type."/server/database/migrations/2019_01_01_0_create_users_table.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveProfilePictureFile($type, $args) {
        $content = ProfilePictureMigrationTemplateController::build($args);
        $file_name = "output-".$type."/server/database/migrations/2019_01_01_1_create_profilepictures_table.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function composerJSONOf($args) {
        $content = ComposerJSONFileTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/composer.json";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function profilePictureModelOf($args) {
        $content = ProfilePictureModelTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/app/Models/profile/Profilepicture.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function userModelOf($args) {
        $content = UserModelTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/app/Models/profile/User.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function profilePictureControllerOf($args) {
        $content = ProfilePictureControllerTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/app/Http/Controllers/profile/ProfilePictureController.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function userControllerlOf($args) {
        $content = UserControllerTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/app/Http/Controllers/profile/UserController.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function profilePictureModelTSOf($args) {
        $content = ProfilePictureModelTSTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/client/src/app/models/profile/ProfilePicture.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        $file_name = "output-".$type."/mobile/src/app/models/profile/ProfilePicture.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function userModelTSOf($args) {
        $content = UserModelTSTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/client/src/app/models/profile/User.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        $file_name = "output-".$type."/mobile/src/app/models/profile/User.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function appBootstrapOf($args) {
        $content = AppBootstrapFileTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/bootstrap/app.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function configMailOf($args) {
        $content = MailFileTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/config/mail.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function configDatabaseOf($args) {
        $content = DatabaseFileTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/config/database.php";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveComponentSpecOf($args, $type, $moduleName) {
        $content = ComponentSpecTemplateController::build($args);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.spec.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveModuleSpecOf($args, $type, $moduleName) {
        $content = ModuleSpecTemplateController::build($args);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".module.spec.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveModuleOf($args, $type, $moduleName, $bddType) {
        $content = ModuleTemplateController::build($args, $type, $moduleName, $bddType);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".module.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveComponentOf($args, $type, $moduleName, $bddType) {
        $content = ComponentTSTemplateController::build($args, $type, $moduleName, $bddType);
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.ts";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveComponentHtmlOf($args, $type, $moduleName, $bddType) {
        $content = "";
        if($type === 'bootstrap') {
            $content = BodyComponentHTMLTemplateController::buildBootstrap($args, $bddType);
        }
        if($type === 'metro') {
            $content = BodyComponentHTMLTemplateController::buildMetro($args, $bddType);
        }
        $tableNameSingular = $args['Table']['nameSingular'];
        $file_name = "output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular."/".strtolower($tableNameSingular).".component.html";
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveControllerOf($args, $type, $bddType) {
        $controller = ControllerTemplateController::build($args, $bddType);
        $table = $controller['Table'];
        $file_name = "output-".$type."/server/app/Http/Controllers/CRUD/".$table['nameSingular']."Controller.php";
        $content = $controller['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Table"=>$table['nameSingular'],"Status"=>$status];
    }

    static function saveMigrationOf($args, $type, $bddType) {
        $migration = MigrationTemplateController::build($args, $bddType);
        $fecha = date('Y_m_d');
        $table = $migration['Table'];
        $migrationIndex = $migration['MigrationIndex'];
        $file_name = "output-".$type."/server/database/migrations/".$fecha.'_'.$migrationIndex."_create_".UtilitiesController::checkNames($table['namePlural'])."_table.php";
        $content = $migration['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Table"=>$table['nameSingular'],"Status"=>$status];
    }

    static function saveModelOf($args, $type, $bddType) {
        $model = ModelTemplateController::build($args, $bddType);
        $tableName = $model['Table'];
        $file_name = "output-".$type."/server/app/Models/".$tableName.".php";
        $content = $model['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Table"=>$tableName,"Status"=>$status];
    }

    static function saveModelTSOf($args, $type, $moduleName, $bddType) {
        $model = ModelTSTemplateController::build($args, $bddType);
        $tableName = $model['Table'];
        $file_name = "output-".$type."/client/src/app/models/".strtoupper($moduleName)."/".$tableName.".ts";
        $content = $model['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $file_name = "output-".$type."/mobile/src/app/models/".strtoupper($moduleName)."/".$tableName.".ts";
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Table"=>$tableName,"Status"=>$status_mobile];
    }

    static function saveServiceTSOf($args, $type, $moduleName) {
        $service = ServiceTSTemplateController::build($args, $moduleName, false);
        $tableName = $service['Table'];
        $file_name = "output-".$type."/client/src/app/services/CRUD/".strtoupper($moduleName)."/".strtolower($tableName).".service.ts";
        $content = $service['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $service = ServiceTSTemplateController::build($args, $moduleName, true);
        $tableName = $service['Table'];
        $file_name = "output-".$type."/mobile/src/app/services/CRUD/".strtoupper($moduleName)."/".strtolower($tableName).".service.ts";
        $content = $service['Content'];
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Table"=>$tableName,"Status"=>$status_mobile];
    }

    static function saveRoutersOf($args) {
        $router = RouterFileTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/server/routes/web.php";
        $content = $router['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveLayoutRoutingModuleOf($args) {
        $type = $args['type'];
        $routingModule =  LayoutRoutingModuleTemplateController::build($args);
        $file_name = "output-".$type."/client/src/app/layout/layout-routing.module.ts";
        $content = $routingModule['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveAuthServicesOf($args) {
        $type = $args['type'];
        $layout_auth_service = AuthTemplateController::build($args, false);
        $file_name = "output-".$type."/client/src/app/services/auth.service.ts";
        $content = $layout_auth_service['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $layout_auth_service = AuthTemplateController::build($args, true);
        $file_name = "output-".$type."/mobile/src/app/services/auth.service.ts";
        $content = $layout_auth_service['Content'];
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status_mobile];
    }

    static function saveProfilePictureServiceOf($args) {
        $type = $args['type'];
        $profile_picture_service = ProfilePictureServiceTemplateController::build($args, false);
        $file_name = "output-".$type."/client/src/app/services/profile/profilepicture.service.ts";
        $content = $profile_picture_service['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $profile_picture_service = ProfilePictureServiceTemplateController::build($args, true);
        $file_name = "output-".$type."/mobile/src/app/services/profile/profilepicture.service.ts";
        $content = $profile_picture_service['Content'];
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status_mobile];
    }

    static function saveUserServiceOf($args) {
        $type = $args['type'];
        $layout_user_service = UserServiceTemplateController::build($args, false);
        $file_name = "output-".$type."/client/src/app/services/profile/user.service.ts";
        $content = $layout_user_service['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $layout_user_service = UserServiceTemplateController::build($args, true);
        $file_name = "output-".$type."/mobile/src/app/services/profile/user.service.ts";
        $content = $layout_user_service['Content'];
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status_mobile];
    }

    static function saveEnvironmentsProdOf($args) {
        $type = $args['type'];
        $environmentProd = EnvironmentsTemplateController::buildEnvironmentsProd($args);
        $file_name = "output-".$type."/client/src/environments/environment.prod.ts";
        $content = $environmentProd['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $file_name = "output-".$type."/mobile/src/environments/environment.prod.ts";
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status_mobile];
    }

    static function saveEnvironmentsOf($args) {
        $type = $args['type'];
        $environment = EnvironmentsTemplateController::buildEnvironments($args);
        $file_name = "output-".$type."/client/src/environments/environment.ts";
        $content = $environment['Content'];
        $status_client = UtilitiesController::saveFile($file_name, $content);
        $file_name = "output-".$type."/mobile/src/environments/environment.ts";
        $status_mobile = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status_mobile];
    }

    static function saveSideBarOf($args) {
        $sidebar =  SideBarTemplateController::build($args);
        $type = $args['type'];
        $file_name = "output-".$type."/client/src/app/components/sidebar/sidebar.component.html";
        $content = $sidebar['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Status"=>$status];
    }

    static function saveMigrationOfMany2Many($args) {
        $relacion = $args['relacion'];
        $type = $args['type'];
        $migration = RelationshipMany2ManyTemplateController::build($relacion);
        $fecha = date('Y_m_d');
        $table = $migration['Table'];
        $migrationIndex = $migration['MigrationIndex'];
        $file_name = "output-".$type."/server/database/migrations/".$fecha.'_'.$migrationIndex."_create_".UtilitiesController::checkNames($table['nameSingular'])."_table.php";
        $content = $migration['Content'];
        $status = UtilitiesController::saveFile($file_name, $content);
        return ["Relationship"=>$table,"Status"=>$status];
    }

    static function saveLayoutOf($args, $type, $moduleName, $bddType) {
        UtilitiesController::createFolder($args, $type, $moduleName);
        FileConstructorController::saveRoutingModuleOf($args, $type, $moduleName);
        FileConstructorController::saveComponentSCSSOf($args, $type, $moduleName);
        FileConstructorController::saveComponentSpecOf($args, $type, $moduleName);
        FileConstructorController::saveModuleSpecOf($args, $type, $moduleName);
        FileConstructorController::saveModuleOf($args, $type, $moduleName, $bddType);
        FileConstructorController::saveComponentOf($args, $type, $moduleName, $bddType);
        FileConstructorController::saveComponentHtmlOf($args, $type, $moduleName, $bddType);
        $status = 'Success';
        $tableName = $args['Table']['nameSingular'];
        $toReturn = ["Table"=>$tableName,"Status"=>$status];
        return $toReturn;
    }
}
