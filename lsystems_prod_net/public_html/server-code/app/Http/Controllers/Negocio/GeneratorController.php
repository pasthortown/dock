<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\FileConstructorController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GeneratorController extends Controller
{
    function saveMigrationOfMany2Many(Request $data) {
        $args = $data->json()->all();
        return response()->json(FileConstructorController::saveMigrationOfMany2Many($args),200);
    }

    function getFromOutput(Request $data) {
        return UtilitiesController::getFromOutput($data);
    }

    function buildAll(Request $data) {
        $args = $data->json()->all();
        $bddType = $args['bddType'];
        $modelos = $args['models'];
        $type = $args['type'];
        $moduleName = $args['moduleName'];
        $relationshipsMany2Many = $args['relationships'];
        FileConstructorController::saveRoutersOf($args);
        FileConstructorController::saveEnvironmentsProdOf($args);
        FileConstructorController::saveEnvironmentsOf($args);
        FileConstructorController::saveLayoutRoutingModuleOf($args);
        FileConstructorController::saveSideBarOf($args);
        FileConstructorController::composerJSONOf($args);
        FileConstructorController::appBootstrapOf($args);
        FileConstructorController::saveEnvFile($type, $args);
        FileConstructorController::saveAuthControllerFile($type, $args);
        FileConstructorController::saveUserMigrationFile($type, $args);
        FileConstructorController::saveProfilePictureFile($type, $args);
        UtilitiesController::makeDirs($args);
        FileConstructorController::saveAuthServicesOf($args);
        FileConstructorController::saveProfilePictureServiceOf($args);
        FileConstructorController::saveUserServiceOf($args);
        FileConstructorController::configMailOf($args);
        FileConstructorController::userModelOf($args);
        FileConstructorController::profilePictureModelOf($args);
        FileConstructorController::userModelTSOf($args);
        FileConstructorController::profilePictureModelTSOf($args);
        FileConstructorController::profilePictureControllerOf($args);
        FileConstructorController::userControllerlOf($args);
        if ($bddType !== "SQL") {
            FileConstructorController::configDatabaseOf($args);
        }
        $log = [];
        foreach ($modelos as $modelo) {
            $ResponseModelTS = FileConstructorController::saveModelTSOf($modelo, $type, $moduleName, $bddType);
            $ResponseServiceTS = FileConstructorController::saveServiceTSOf($modelo, $type, $moduleName);
            $ResponseModel = FileConstructorController::saveModelOf($modelo, $type, $bddType);
            $ResponseMigration = FileConstructorController::saveMigrationOf($modelo, $type, $bddType);
            $ResponseController = FileConstructorController::saveControllerOf($modelo, $type, $bddType);
            $ResponseLayout = FileConstructorController::saveLayoutOf($modelo, $type, $moduleName, $bddType);
            $register = ["Name"=>$modelo['Table']['nameSingular'], "Model_File"=>$ResponseModel, "Migration_File"=>$ResponseMigration, "Controller_File"=>$ResponseController, "ModelTS_File"=>$ResponseModelTS, "ServiceTS_File"=>$ResponseServiceTS, "Client_Layout"=>$ResponseLayout];
            array_push($log,$register);
        }
        foreach($relationshipsMany2Many as $relationshipData) {
            $logRelationship = FileConstructorController::saveMigrationOfMany2Many($relationshipData);
            array_push($log, $logRelationship);
        }
        $file = UtilitiesController::getFromOutput(['q'=>$type]);
        return response()->json(["file"=>$file, "log"=>$log], 200);
    }
}
