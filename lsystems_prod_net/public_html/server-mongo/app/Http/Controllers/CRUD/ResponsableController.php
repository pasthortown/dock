<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Responsable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponsableController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Responsable::all(),200);
       } else {
          return response()->json(Responsable::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Responsable::count();
       $result = Responsable::offset($offset)->limit(intval($size))->get();
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
          $lastResponsable = Responsable::orderBy('id', 'desc')->first();
          if($lastResponsable) {
             $id = $lastResponsable->id + 1;
          } else {
             $id = 1;
          }
          $responsable = Responsable::create([
             'id' => $id,
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($responsable,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $responsable = Responsable::find(intval($result['id']));
          $responsable->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($responsable,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $responsable = Responsable::find(intval($id));
       return response()->json($responsable->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Responsable::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Responsable::where('id',$result['id'])->first();
         if ($exist) {
          $responsable = Responsable::find(intval($result['id']));
          $responsable->save();
         } else {
          $responsable = Responsable::create([
             'id' => $result['id'],
          ]);
         }
       }
    }
}