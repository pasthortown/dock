<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ScheduleResourceAssistant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleResourceAssistantController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(ScheduleResourceAssistant::all(),200);
       } else {
          return response()->json(ScheduleResourceAssistant::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ScheduleResourceAssistant::count();
       $result = ScheduleResourceAssistant::offset($offset)->limit(intval($size))->get();
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
          $lastScheduleResourceAssistant = ScheduleResourceAssistant::orderBy('id', 'desc')->first();
          if($lastScheduleResourceAssistant) {
             $id = $lastScheduleResourceAssistant->id + 1;
          } else {
             $id = 1;
          }
          $scheduleresourceassistant = ScheduleResourceAssistant::create([
             'id' => $id,
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresourceassistant,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $scheduleresourceassistant = ScheduleResourceAssistant::find(intval($result['id']));
          $scheduleresourceassistant->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($scheduleresourceassistant,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $scheduleresourceassistant = ScheduleResourceAssistant::find(intval($id));
       return response()->json($scheduleresourceassistant->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = ScheduleResourceAssistant::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = ScheduleResourceAssistant::where('id',$result['id'])->first();
         if ($exist) {
          $scheduleresourceassistant = ScheduleResourceAssistant::find(intval($result['id']));
          $scheduleresourceassistant->save();
         } else {
          $scheduleresourceassistant = ScheduleResourceAssistant::create([
             'id' => $result['id'],
          ]);
         }
       }
    }
}