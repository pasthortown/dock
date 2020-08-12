<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Storage;
use ZipArchive;

class UtilitiesController extends Controller
{
    static function checkNames($input) {
        $output = '';
        $estadoAnterior = "lower";
        foreach(str_split($input) as $char) {
            if ($char === strtoupper($char)){
                if ($estadoAnterior === "lower") {
                    $estadoAnterior = "upper";
                  $output .= '_';
                }
            } else {
                if ($estadoAnterior === "upper") {
                    $estadoAnterior = "lower";
                }
            }
            $output .= strtolower($char);
        }
        return trim($output,'_');
    }

    static function saveFile($file_name, $content) {
        $status = 'Success';
        try{
            Storage::disk('local')->put('factory/'.$file_name, $content);
        } catch (Exception $e) {
            $status = $e->getMessage();
        }
        return $status;
    }

    static function makeDirs($args) {
        $moduleName = $args['moduleName'];
        $type = $args['type'];
        $status = "Success";
        try{
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/models/".strtoupper($moduleName));
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/services/CRUD/".strtoupper($moduleName));
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/models/".strtoupper($moduleName));
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/services/CRUD/".strtoupper($moduleName));
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName));
        } catch (Exception $e) {
            $status = $e->getMessage();
        }
        return $status;
    }

    static function createFolder($args, $type, $moduleName) {
        $tableNameSingular = $args['Table']['nameSingular'];
        $status = "Success";
        try{
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/layout/CRUD/".strtoupper($moduleName)."/".$tableNameSingular);
        } catch (Exception $e) {
            $status = $e->getMessage();
        }
        return $status;
    }

    static function cleanOutput($type) {
        $status = "Success";
        try{
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/server/app/Http/Controllers/CRUD/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/server/app/Models/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/server/database/migrations/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/client/src/app/models/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/mobile/src/app/models/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/client/src/app/services/CRUD/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/mobile/src/app/services/CRUD/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/client/src/app/layout/CRUD/");
            Storage::disk('local')->deleteDirectory("factory/output-".$type."/server/config/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/server/database/migrations/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/models/profile/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/models/profile/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/services/CRUD/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/services/CRUD/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/services/CRUD/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/server/config/");
            Storage::disk('local')->put("factory/output-".$type."/server/app/Http/Controllers/CRUD/readme.txt", 'Add here your own CRUD Controllers');
            Storage::disk('local')->put("factory/output-".$type."/server/app/Models/readme.txt", 'Add here your own Models');
            Storage::disk('local')->makeDirectory("factory/output-".$type."/client/src/app/models/profile/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/mobile/src/app/models/profile/");
            Storage::disk('local')->makeDirectory("factory/output-".$type."/server/app/Models/profile/");
            Storage::disk('local')->copy("factory/models/profile/ProfilePicture.ts", "factory/output-".$type."/client/src/app/models/profile/ProfilePicture.ts");
            Storage::disk('local')->copy("factory/models/profile/User.ts", "factory/output-".$type."/client/src/app/models/profile/User.ts");
            Storage::disk('local')->copy("factory/models/profile/ProfilePicture.ts", "factory/output-".$type."/mobile/src/app/models/profile/ProfilePicture.ts");
            Storage::disk('local')->copy("factory/models/profile/User.ts", "factory/output-".$type."/mobile/src/app/models/profile/User.ts");
            Storage::disk('local')->copy("factory/migrationsProfile/2019_01_01_0_create_users_table.php", "factory/output-".$type."/server/database/migrations/2019_01_01_0_create_users_table.php");
            Storage::disk('local')->copy("factory/migrationsProfile/2019_01_01_1_create_profilepictures_table.php", "factory/output-".$type."/server/database/migrations/2019_01_01_1_create_profilepictures_table.php");
            Storage::disk('local')->copy("factory/modelsserver/profile/Profilepicture.php", "factory/output-".$type."/server/app/Models/profile/Profilepicture.php");
            Storage::disk('local')->copy("factory/modelsserver/profile/User.php", "factory/output-".$type."/server/app/Models/profile/User.php");
            Storage::disk('local')->delete("factory/output.zip");
        } catch (Exception $e) {
            $status = $e->getMessage();
        }
        return $status;
    }

    static function getFromOutput($args) {
        $zip = new ZipArchive();
        $type = $args['q'];
        $fileNameZipOutput = 'output.zip';
        $zip->open(storage_path('app/factory/').$fileNameZipOutput, ZipArchive::CREATE);
        $files = Storage::disk('local')->allFiles('factory/output-'.$type);
        foreach ($files as $file) {
            $relativePath = substr( $file, strlen('factory/output-'.$type) + 1);
            $zip->addFile(storage_path('app/').$file, $relativePath);
        }
        $zip->close();
        $result = base64_encode(Storage::disk('local')->get("factory/output.zip"));
        UtilitiesController::cleanOutput($type);
        return $result;
    }
}
