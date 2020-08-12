<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Resource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Resource::all(),200);
       } else {
          return response()->json(Resource::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Resource::count();
       $result = Resource::offset($offset)->limit(intval($size))->get();
       $toReturn = new LengthAwarePaginator($result, $total, $size, $currentPage, [
          'path' => Paginator::resolveCurrentPath(),
          'pageName' => 'page'
       ]);
       return response()->json($toReturn,200);
    }

    function post(Request $data)
    {
       try{
          $result = $data->json()->all();
          $lastResource = Resource::orderBy('id', 'desc')->first();
          if($lastResource) {
             $id = $lastResource->id + 1;
          } else {
             $id = 1;
          }
          $resource = Resource::create([
             'id' => $id,
             'fullname'=>$result['fullname'],
             'join_data'=>$result['join_data'],
             'capacity'=>$result['capacity'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resource,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $resource = Resource::find(intval($result['id']));
          $resource->fullname = $result['fullname'];
          $resource->join_data = $result['join_data'];
          $resource->capacity = $result['capacity'];
          $resource->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resource,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $resource = Resource::find(intval($id));
       return response()->json($resource->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Resource::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Resource::where('id',$result['id'])->first();
         if ($exist) {
          $resource = Resource::find(intval($result['id']));
          $resource->fullname = $result['fullname'];
          $resource->join_data = $result['join_data'];
          $resource->capacity = $result['capacity'];
          $resource->save();
         } else {
          $resource = Resource::create([
             'id' => $result['id'],
             'fullname'=>$result['fullname'],
             'join_data'=>$result['join_data'],
             'capacity'=>$result['capacity'],
          ]);
         }
       }
    }
}