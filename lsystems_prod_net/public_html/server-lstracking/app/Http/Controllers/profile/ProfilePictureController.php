<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\ProfilePicture;
use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfilePictureController extends Controller
{
    function get(Request $data)
    {
       $user_id = $data['user_id'];
       $profilepicture = ProfilePicture::where('id_user', intval($user_id))->first();
       if ($profilepicture) {
         return response()->json($profilepicture,200);
       }else {
         return response()->json(['id'=>0, 'file_type'=>'', 'file_name'=>'', 'file'=>''],200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = ProfilePicture::count();
       $result = ProfilePicture::offset($offset)->limit(intval($size))->get();
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
          $lastProfilePicture = ProfilePicture::orderBy('id', 'desc')->first();
          if($lastProfilePicture) {
             $id = $lastProfilePicture->id + 1;
          } else {
             $id = 1;
          }
          $profilePicture = ProfilePicture::create([
             'id' => $id,
             'id_user' => $data->auth->id,
             'file_type'=>$result['file_type'],
             'file_name'=>$result['file_name'],
             'file'=>$result['file'],
          ]);
          return response()->json($profilepicture,200);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $profilepicture = ProfilePicture::find(intval($result['id']));
          $profilepicture->file_type = $result['file_type'];
          $profilepicture->file_name = $result['file_name'];
          $profilepicture->file = $result['file'];
          $profilepicture->save();
          return response()->json($profilepicture,200);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
    }

    function delete(Request $data)
    {
       $result = $data->json()->all();
       $id = $result['id'];
       $profilePicture = ProfilePicture::find(intval($id));
       return response()->json($profilePicture->delete(),200);
    }
}
