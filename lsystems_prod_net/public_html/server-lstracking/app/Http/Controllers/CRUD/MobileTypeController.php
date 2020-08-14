<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\MobileType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileTypeController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(MobileType::all(),200);
       } else {
          return response()->json(MobileType::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = MobileType::count();
       $result = MobileType::offset($offset)->limit(intval($size))->get();
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
          $lastMobileType = MobileType::orderBy('id', 'desc')->first();
          if($lastMobileType) {
             $id = $lastMobileType->id + 1;
          } else {
             $id = 1;
          }
          $mobiletype = MobileType::create([
             'id' => $id,
             'name'=>$result['name'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobiletype,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $mobiletype = MobileType::find(intval($result['id']));
          $mobiletype->name = $result['name'];
          $mobiletype->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobiletype,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $mobiletype = MobileType::find(intval($id));
       return response()->json($mobiletype->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = MobileType::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = MobileType::where('id',$result['id'])->first();
         if ($exist) {
          $mobiletype = MobileType::find(intval($result['id']));
          $mobiletype->name = $result['name'];
          $mobiletype->save();
         } else {
          $mobiletype = MobileType::create([
             'id' => $result['id'],
             'name'=>$result['name'],
          ]);
         }
       }
    }
}