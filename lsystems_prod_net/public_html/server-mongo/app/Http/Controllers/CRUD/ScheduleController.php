<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Schedule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Schedule::all(),200);
       } else {
          return response()->json(Schedule::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Schedule::count();
       $result = Schedule::offset($offset)->limit(intval($size))->get();
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
          $lastSchedule = Schedule::orderBy('id', 'desc')->first();
          if($lastSchedule) {
             $id = $lastSchedule->id + 1;
          } else {
             $id = 1;
          }
          $schedule = Schedule::create([
             'id' => $id,
             'start_time'=>$result['start_time'],
             'end_time'=>$result['end_time'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($schedule,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $schedule = Schedule::find(intval($result['id']));
          $schedule->start_time = $result['start_time'];
          $schedule->end_time = $result['end_time'];
          $schedule->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($schedule,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $schedule = Schedule::find(intval($id));
       return response()->json($schedule->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Schedule::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Schedule::where('id',$result['id'])->first();
         if ($exist) {
          $schedule = Schedule::find(intval($result['id']));
          $schedule->start_time = $result['start_time'];
          $schedule->end_time = $result['end_time'];
          $schedule->save();
         } else {
          $schedule = Schedule::create([
             'id' => $result['id'],
             'start_time'=>$result['start_time'],
             'end_time'=>$result['end_time'],
          ]);
         }
       }
    }
}