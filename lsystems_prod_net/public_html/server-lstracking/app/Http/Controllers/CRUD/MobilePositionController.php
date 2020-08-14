<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\MobilePosition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MobilePositionController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(MobilePosition::all(),200);
       } else {
          return response()->json(MobilePosition::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = MobilePosition::count();
       $result = MobilePosition::offset($offset)->limit(intval($size))->get();
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
          $lastMobilePosition = MobilePosition::orderBy('id', 'desc')->first();
          if($lastMobilePosition) {
             $id = $lastMobilePosition->id + 1;
          } else {
             $id = 1;
          }
          $mobileposition = MobilePosition::create([
             'id' => $id,
             'id_mobile' => $result['id_mobile'],
             'ubication' => $result['ubication'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobileposition,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $mobileposition = MobilePosition::find(intval($result['id']));
          $mobileposition->id_mobile = $result['id_mobile'];
             $mobileposition->ubication = $result['ubication'];
          $mobileposition->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobileposition,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $mobileposition = MobilePosition::find(intval($id));
       return response()->json($mobileposition->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = MobilePosition::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = MobilePosition::where('id',$result['id'])->first();
         if ($exist) {
          $mobileposition = MobilePosition::find(intval($result['id']));
          $mobileposition->id_mobile = $result['id_mobile'];
             $mobileposition->ubication = $result['ubication'];
          $mobileposition->save();
         } else {
          $mobileposition = MobilePosition::create([
             'id' => $result['id'],
             'id_mobile'=>$result['id_mobile'],
             'ubication' => $result['ubication'],
          ]);
         }
       }
    }
}
