<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ProjectType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectTypeController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ProjectType::get(),200);
       } else {
          $projecttype = ProjectType::findOrFail($id);
          $attach = [];
          return response()->json(["ProjectType"=>$projecttype, "attach"=>$attach],200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       return response()->json(ProjectType::paginate($size),200);
    }

    function post(Request $data)
    {
       try{
          DB::beginTransaction();
          $result = $data->json()->all();
          $projecttype = new ProjectType();
          $lastProjectType = ProjectType::orderBy('id')->get()->last();
          if($lastProjectType) {
             $projecttype->id = $lastProjectType->id + 1;
          } else {
             $projecttype->id = 1;
          }
          $projecttype->name = $result['name'];
          $projecttype->save();
          DB::commit();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($projecttype,200);
    }

    function put(Request $data)
    {
       try{
          DB::beginTransaction();
          $result = $data->json()->all();
          $projecttype = ProjectType::where('id',$result['id'])->update([
             'name'=>$result['name'],
          ]);
          DB::commit();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($projecttype,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       return ProjectType::destroy($id);
    }

    function backup(Request $data)
    {
       $projecttypes = ProjectType::get();
       $toReturn = [];
       foreach( $projecttypes as $projecttype) {
          $attach = [];
          array_push($toReturn, ["ProjectType"=>$projecttype, "attach"=>$attach]);
       }
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
      try{
       DB::beginTransaction();
       foreach($masiveData as $row) {
         $result = $row['ProjectType'];
         $exist = ProjectType::where('id',$result['id'])->first();
         if ($exist) {
           ProjectType::where('id', $result['id'])->update([
             'name'=>$result['name'],
           ]);
         } else {
          $projecttype = new ProjectType();
          $projecttype->id = $result['id'];
          $projecttype->name = $result['name'];
          $projecttype->save();
         }
       }
       DB::commit();
      } catch (Exception $e) {
         return response()->json($e,400);
      }
      return response()->json('Task Complete',200);
    }
}