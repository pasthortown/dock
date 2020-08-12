<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ScheduleResourceAssigment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleResourceAssigmentController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ScheduleResourceAssigment::all(),200);
       } else {
          return response()->json(ScheduleResourceAssigment::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ScheduleResourceAssigment::count();
       $result = ScheduleResourceAssigment::offset($offset)->limit(intval($size))->get();
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
          $lastScheduleResourceAssigment = ScheduleResourceAssigment::orderBy('id', 'desc')->first();
          if($lastScheduleResourceAssigment) {
             $id = $lastScheduleResourceAssigment->id + 1;
          } else {
             $id = 1;
          }
          $scheduleresourceassigment = ScheduleResourceAssigment::create([
             'id' => $id,
             'date'=>$result['date'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresourceassigment,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $scheduleresourceassigment = ScheduleResourceAssigment::find(intval($result['id']));
          $scheduleresourceassigment->date = $result['date'];
          $scheduleresourceassigment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresourceassigment,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $scheduleresourceassigment = ScheduleResourceAssigment::find(intval($id));
       return response()->json($scheduleresourceassigment->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ScheduleResourceAssigment::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ScheduleResourceAssigment::where('id',$result['id'])->first();
         if ($exist) {
          $scheduleresourceassigment = ScheduleResourceAssigment::find(intval($result['id']));
          $scheduleresourceassigment->date = $result['date'];
          $scheduleresourceassigment->save();
         } else {
          $scheduleresourceassigment = ScheduleResourceAssigment::create([
             'id' => $result['id'],
             'date'=>$result['date'],
          ]);
         }
       }
    }
}