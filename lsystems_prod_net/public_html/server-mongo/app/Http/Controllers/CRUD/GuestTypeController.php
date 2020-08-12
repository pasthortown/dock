<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\GuestType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestTypeController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(GuestType::all(),200);
       } else {
          return response()->json(GuestType::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = GuestType::count();
       $result = GuestType::offset($offset)->limit(intval($size))->get();
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
          $lastGuestType = GuestType::orderBy('id', 'desc')->first();
          if($lastGuestType) {
             $id = $lastGuestType->id + 1;
          } else {
             $id = 1;
          }
          $guesttype = GuestType::create([
             'id' => $id,
             'name'=>$result['name'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($guesttype,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $guesttype = GuestType::find(intval($result['id']));
          $guesttype->name = $result['name'];
          $guesttype->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($guesttype,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $guesttype = GuestType::find(intval($id));
       return response()->json($guesttype->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = GuestType::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = GuestType::where('id',$result['id'])->first();
         if ($exist) {
          $guesttype = GuestType::find(intval($result['id']));
          $guesttype->name = $result['name'];
          $guesttype->save();
         } else {
          $guesttype = GuestType::create([
             'id' => $result['id'],
             'name'=>$result['name'],
          ]);
         }
       }
    }
}