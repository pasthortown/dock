<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DownloadController extends Controller
{
    function info(Request $data)
    {
        $result = $data->json()->all();
        $urlToDownload = $result['urlToDownload'];
        $r = shell_exec("youtube-dl " . $urlToDownload . " -F");
        $output = explode("\n", $r);
        $toPreReturn = [];
        $initialIndex = 0;
        $lastIndex = 0;
        $toReturn = [];
        for ($i = 0; $i < sizeof($output); $i++) {
            $line = explode(" ", $output[$i]);
            if ($output[$i] == "") {
                $lastIndex = $i - 1;
            } else {
                if (sizeof($line) > 1) {
                    if ($line[0] == "format") {
                        $initialIndex = $i + 1;
                    }
                }
            }
        }
        $toPreReturn = array_slice($output, $initialIndex, $lastIndex - $initialIndex + 1);
        foreach($toPreReturn as $preDataLine) {
            $line = explode(" ", $preDataLine);
            $id = $line[0];
            $format = trim(substr($preDataLine, strlen($id), strlen($preDataLine) - strlen($id)));
            array_push($toReturn, ["id"=>$id, "format"=>$format]);
        }
        return response()->json($toReturn,200);
    }

    function one(Request $data)
    {
        $result = $data->json()->all();
        $urlToDownload = $result['urlToDownload'];
        $format = $result['format'];
        $ToMP3 = "NO";
        if ($format == 999) {
            $ToMP3 = "SI";
            $format = 251;
        }
        $result = shell_exec("./download_one.h " . $urlToDownload . " " . $ToMP3 . " " . $format);
        return response()->json($result,200);
    }
    
    function multiple(Request $data)
    {
        $result = $data->json()->all();
        $folderToDownload = $result['folderToDownload'];
        $folderToDownloadFIN = $folderToDownload;
        $folderToDownloadPrev = explode(" ", $folderToDownload);
        $format = $result['format'];
        if (sizeof($folderToDownloadPrev) > 1) {
            $folderToDownloadFIN = implode("_", $folderToDownloadPrev);
        }
        $urlToDownload = $result['urlToDownload'];
        $ToMP3 = "NO";
        if ($format == 999) {
            $ToMP3 = "SI";
            $format = 251;
        }
        $result = shell_exec("./download_multiple.h " . $folderToDownloadFIN . " " . $urlToDownload . " " . $ToMP3 . " " . $format);
        return response()->json($result,200);
    }
}