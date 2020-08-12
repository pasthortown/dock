<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ResourceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceTypeController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ResourceType::all(),200);
       } else {
          return response()->json(ResourceType::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ResourceType::count();
       $result = ResourceType::offset($offset)->limit(intval($size))->get();
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
          $lastResourceType = ResourceType::orderBy('id', 'desc')->first();
          if($lastResourceType) {
             $id = $lastResourceType->id + 1;
          } else {
             $id = 1;
          }
          $resourcetype = ResourceType::create([
             'id' => $id,
             'name'=>$result['name'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resourcetype,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $resourcetype = ResourceType::find(intval($result['id']));
          $resourcetype->name = $result['name'];
          $resourcetype->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($resourcetype,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $resourcetype = ResourceType::find(intval($id));
       return response()->json($resourcetype->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ResourceType::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ResourceType::where('id',$result['id'])->first();
         if ($exist) {
          $resourcetype = ResourceType::find(intval($result['id']));
          $resourcetype->name = $result['name'];
          $resourcetype->save();
         } else {
          $resourcetype = ResourceType::create([
             'id' => $result['id'],
             'name'=>$result['name'],
          ]);
         }
       }
    }
}