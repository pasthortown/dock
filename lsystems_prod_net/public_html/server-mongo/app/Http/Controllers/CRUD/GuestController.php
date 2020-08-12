<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Guest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class GuestController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Guest::all(),200);
       } else {
          return response()->json(Guest::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = Guest::count();
       $result = Guest::offset($offset)->limit(intval($size))->get();
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
          $lastGuest = Guest::orderBy('id', 'desc')->first();
          if($lastGuest) {
             $id = $lastGuest->id + 1;
          } else {
             $id = 1;
          }
          $guest = Guest::create([
             'id' => $id,
             'name'=>$result['name'],
             'email'=>$result['email'],
             'identification'=>$result['identification'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($guest,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $guest = Guest::find(intval($result['id']));
          $guest->name = $result['name'];
          $guest->email = $result['email'];
          $guest->identification = $result['identification'];
          $guest->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($guest,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $guest = Guest::find(intval($id));
       return response()->json($guest->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = Guest::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = Guest::where('id',$result['id'])->first();
         if ($exist) {
          $guest = Guest::find(intval($result['id']));
          $guest->name = $result['name'];
          $guest->email = $result['email'];
          $guest->identification = $result['identification'];
          $guest->save();
         } else {
          $guest = Guest::create([
             'id' => $result['id'],
             'name'=>$result['name'],
             'email'=>$result['email'],
             'identification'=>$result['identification'],
          ]);
         }
       }
    }
}