<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\MobileAttachment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileAttachmentController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(MobileAttachment::all(),200);
       } else {
          return response()->json(MobileAttachment::find(intval($id)),200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       $currentPage = $data->input('page', 1);
       $offset = ($currentPage - 1) * $size;
       $total = MobileAttachment::count();
       $result = MobileAttachment::offset($offset)->limit(intval($size))->get();
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
          $lastMobileAttachment = MobileAttachment::orderBy('id', 'desc')->first();
          if($lastMobileAttachment) {
             $id = $lastMobileAttachment->id + 1;
          } else {
             $id = 1;
          }
          $mobileattachment = MobileAttachment::create([
             'id' => $id,
             'mobile_attachment_file_type'=>$result['mobile_attachment_file_type'],
             'mobile_attachment_file_name'=>$result['mobile_attachment_file_name'],
             'mobile_attachment_file'=>$result['mobile_attachment_file'],
          ]);
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobileattachment,200);
    }

    function put(Request $data)
    {
       try{
          $result = $data->json()->all();
          $mobileattachment = MobileAttachment::find(intval($result['id']));
          $mobileattachment->mobile_attachment_file_type = $result['mobile_attachment_file_type'];
          $mobileattachment->mobile_attachment_file_name = $result['mobile_attachment_file_name'];
          $mobileattachment->mobile_attachment_file = $result['mobile_attachment_file'];
          $mobileattachment->save();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($mobileattachment,200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       $mobileattachment = MobileAttachment::find(intval($id));
       return response()->json($mobileattachment->delete(),200);
    }

    function backup(Request $data)
    {
       $toReturn = MobileAttachment::all();
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
       foreach($masiveData as $result) {
         $exist = MobileAttachment::where('id',$result['id'])->first();
         if ($exist) {
          $mobileattachment = MobileAttachment::find(intval($result['id']));
          $mobileattachment->mobile_attachment_file_type = $result['mobile_attachment_file_type'];
          $mobileattachment->mobile_attachment_file_name = $result['mobile_attachment_file_name'];
          $mobileattachment->mobile_attachment_file = $result['mobile_attachment_file'];
          $mobileattachment->save();
         } else {
          $mobileattachment = MobileAttachment::create([
             'id' => $result['id'],
             'mobile_attachment_file_type'=>$result['mobile_attachment_file_type'],
             'mobile_attachment_file_name'=>$result['mobile_attachment_file_name'],
             'mobile_attachment_file'=>$result['mobile_attachment_file'],
          ]);
         }
       }
    }
}