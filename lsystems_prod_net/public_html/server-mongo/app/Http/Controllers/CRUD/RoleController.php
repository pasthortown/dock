<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Role::all(),200);
       } else {
          return response()->json(Role::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Role::count();
       $result = Role::offset($offset)->limit(intval($size))->get();
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
          $lastRole = Role::orderBy('id', 'desc')->first();
          if($lastRole) {
             $id = $lastRole->id + 1;
          } else {
             $id = 1;
          }
          $role = Role::create([
             'id' => $id,
             'name'=>$result['name'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($role,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $role = Role::find(intval($result['id']));
          $role->name = $result['name'];
          $role->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($role,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $role = Role::find(intval($id));
       return response()->json($role->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Role::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Role::where('id',$result['id'])->first();
         if ($exist) {
          $role = Role::find(intval($result['id']));
          $role->name = $result['name'];
          $role->save();
         } else {
          $role = Role::create([
             'id' => $result['id'],
             'name'=>$result['name'],
          ]);
         }
       }
    }
}