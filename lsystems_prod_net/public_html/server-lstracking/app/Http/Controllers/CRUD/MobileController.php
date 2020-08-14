<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Mobile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Mobile::all(),200);
       } else {
          return response()->json(Mobile::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Mobile::count();
       $result = Mobile::offset($offset)->limit(intval($size))->get();
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
          $lastMobile = Mobile::orderBy('id', 'desc')->first();
          if($lastMobile) {
             $id = $lastMobile->id + 1;
          } else {
             $id = 1;
          }
          $mobile = Mobile::create([
             'id' => $id,
             'name'=>$result['name'],
             'description'=>$result['description'],
             'number'=>$result['number'],
             'id_user'=>$result['id_user'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobile,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $mobile = Mobile::find(intval($result['id']));
          $mobile->name = $result['name'];
          $mobile->description = $result['description'];
          $mobile->number = $result['number'];
          $mobile->id_user = $result['id_user'];
          $mobile->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobile,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $mobile = Mobile::find(intval($id));
       return response()->json($mobile->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Mobile::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Mobile::where('id',$result['id'])->first();
         if ($exist) {
          $mobile = Mobile::find(intval($result['id']));
          $mobile->name = $result['name'];
          $mobile->description = $result['description'];
          $mobile->number = $result['number'];
          $mobile->id_user = $result['id_user'];
          $mobile->save();
         } else {
          $mobile = Mobile::create([
             'id' => $result['id'],
             'name'=>$result['name'],
             'description'=>$result['description'],
             'number'=>$result['number'],
             'id_user'=>$result['id_user'],
          ]);
         }
       }
    }
}