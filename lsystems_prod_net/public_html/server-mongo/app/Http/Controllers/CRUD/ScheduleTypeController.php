<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ScheduleType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleTypeController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ScheduleType::all(),200);
       } else {
          return response()->json(ScheduleType::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ScheduleType::count();
       $result = ScheduleType::offset($offset)->limit(intval($size))->get();
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
          $lastScheduleType = ScheduleType::orderBy('id', 'desc')->first();
          if($lastScheduleType) {
             $id = $lastScheduleType->id + 1;
          } else {
             $id = 1;
          }
          $scheduletype = ScheduleType::create([
             'id' => $id,
             'name'=>$result['name'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduletype,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $scheduletype = ScheduleType::find(intval($result['id']));
          $scheduletype->name = $result['name'];
          $scheduletype->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduletype,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $scheduletype = ScheduleType::find(intval($id));
       return response()->json($scheduletype->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ScheduleType::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ScheduleType::where('id',$result['id'])->first();
         if ($exist) {
          $scheduletype = ScheduleType::find(intval($result['id']));
          $scheduletype->name = $result['name'];
          $scheduletype->save();
         } else {
          $scheduletype = ScheduleType::create([
             'id' => $result['id'],
             'name'=>$result['name'],
          ]);
         }
       }
    }
}