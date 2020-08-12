<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ScheduleResponsableAssigment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleResponsableAssigmentController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ScheduleResponsableAssigment::all(),200);
       } else {
          return response()->json(ScheduleResponsableAssigment::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ScheduleResponsableAssigment::count();
       $result = ScheduleResponsableAssigment::offset($offset)->limit(intval($size))->get();
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
          $lastScheduleResponsableAssigment = ScheduleResponsableAssigment::orderBy('id', 'desc')->first();
          if($lastScheduleResponsableAssigment) {
             $id = $lastScheduleResponsableAssigment->id + 1;
          } else {
             $id = 1;
          }
          $scheduleresponsableassigment = ScheduleResponsableAssigment::create([
             'id' => $id,
             'date'=>$result['date'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresponsableassigment,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $scheduleresponsableassigment = ScheduleResponsableAssigment::find(intval($result['id']));
          $scheduleresponsableassigment->date = $result['date'];
          $scheduleresponsableassigment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresponsableassigment,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $scheduleresponsableassigment = ScheduleResponsableAssigment::find(intval($id));
       return response()->json($scheduleresponsableassigment->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ScheduleResponsableAssigment::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ScheduleResponsableAssigment::where('id',$result['id'])->first();
         if ($exist) {
          $scheduleresponsableassigment = ScheduleResponsableAssigment::find(intval($result['id']));
          $scheduleresponsableassigment->date = $result['date'];
          $scheduleresponsableassigment->save();
         } else {
          $scheduleresponsableassigment = ScheduleResponsableAssigment::create([
             'id' => $result['id'],
             'date'=>$result['date'],
          ]);
         }
       }
    }
}