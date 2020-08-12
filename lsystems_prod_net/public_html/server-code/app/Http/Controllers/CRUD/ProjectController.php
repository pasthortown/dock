<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Exception;
use App\Project;
use App\ProjectAttachment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectController extends Controller
{
    function get(Request $data)
    {
       $id = $data['id'];
       if ($id == null) {
          return response()->json(Project::get(),200);
       } else {
          $project = Project::findOrFail($id);
          $attach = ProjectAttachment::where('project_id', $project->id)->get();
          return response()->json(["Project"=>$project, "attach"=>$attach],200);
       }
    }

    function paginate(Request $data)
    {
       $size = $data['size'];
       return response()->json(Project::paginate($size),200);
    }

    function my_projects(Request $data)
    {
        $result = $data->json()->all();
        $projects = Project::where('user_id', $result['user_id'])->where('project_type_id', $result['project_type_id'])->select('id','name','date')->orderBy('date')->get();
        return response()->json($projects,200);
    }

    function post(Request $data)
    {
       try{
          DB::beginTransaction();
          $result = $data->json()->all();
          $project = new Project();
          $lastProject = Project::orderBy('id')->get()->last();
          if($lastProject) {
             $project->id = $lastProject->id + 1;
          } else {
             $project->id = 1;
          }
          $project->name = $result['name'];
          $project->date = $result['date'];
          $project->structure = $result['structure'];
          $project->user_id = $result['user_id'];
          $project->project_type_id = $result['project_type_id'];
          $project->save();
          $preview_project_attachments = ProjectAttachment::where('project_id', $project->id)->get();
          foreach($preview_project_attachments as $preview_project_attachment) {
            ProjectAttachment::destroy($preview_project_attachment->id);
          }
          $new_project_attachments = $result['attachments'];
          foreach($new_project_attachments as $new_project_attachment) {
            $projectattachment = new ProjectAttachment();
            $lastProjectAttachment = ProjectAttachment::orderBy('id')->get()->last();
            if($lastProjectAttachment) {
               $projectattachment->id = $lastProjectAttachment->id + 1;
            } else {
               $projectattachment->id = 1;
            }
            $projectattachment->project_attachment_file_type = $new_project_attachment['project_attachment_file_type'];
            $projectattachment->project_attachment_file_name = $new_project_attachment['project_attachment_file_name'];
            $projectattachment->project_attachment_file = $new_project_attachment['project_attachment_file'];
            $projectattachment->project_id = $project->id;
            $projectattachment->save();
          }
          DB::commit();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json($project,200);
    }

    function put(Request $data)
    {
       try{
          DB::beginTransaction();
          $result = $data->json()->all();
          $project = Project::where('id',$result['id'])->update([
             'name'=>$result['name'],
             'date'=>$result['date'],
             'structure'=>$result['structure'],
             'user_id'=>$result['user_id'],
             'project_type_id'=>$result['project_type_id'],
          ]);
          $preview_project_attachments = ProjectAttachment::where('project_id', $result['id'])->get();
          foreach($preview_project_attachments as $preview_project_attachment) {
            ProjectAttachment::destroy($preview_project_attachment->id);
          }
          $new_project_attachments = $result['attachments'];
          foreach($new_project_attachments as $new_project_attachment) {
            $projectattachment = new ProjectAttachment();
            $lastProjectAttachment = ProjectAttachment::orderBy('id')->get()->last();
            if($lastProjectAttachment) {
               $projectattachment->id = $lastProjectAttachment->id + 1;
            } else {
               $projectattachment->id = 1;
            }
            $projectattachment->project_attachment_file_type = $new_project_attachment['project_attachment_file_type'];
            $projectattachment->project_attachment_file_name = $new_project_attachment['project_attachment_file_name'];
            $projectattachment->project_attachment_file = $new_project_attachment['project_attachment_file'];
            $projectattachment->project_id = $result['id'];
            $projectattachment->save();
          }
          DB::commit();
       } catch (Exception $e) {
          return response()->json($e,400);
       }
       return response()->json(['id'=>$result['id']],200);
    }

    function delete(Request $data)
    {
       $id = $data['id'];
       return Project::destroy($id);
    }

    function backup(Request $data)
    {
       $projects = Project::get();
       $toReturn = [];
       foreach( $projects as $project) {
          $attach = [];
          array_push($toReturn, ["Project"=>$project, "attach"=>$attach]);
       }
       return response()->json($toReturn,200);
    }

    function masiveLoad(Request $data)
    {
      $incomming = $data->json()->all();
      $masiveData = $incomming['data'];
      try{
       DB::beginTransaction();
       foreach($masiveData as $row) {
         $result = $row['Project'];
         $exist = Project::where('id',$result['id'])->first();
         if ($exist) {
           Project::where('id', $result['id'])->update([
             'name'=>$result['name'],
             'date'=>$result['date'],
             'structure'=>$result['structure'],
             'user_id'=>$result['user_id'],
             'project_type_id'=>$result['project_type_id'],
           ]);
         } else {
          $project = new Project();
          $project->id = $result['id'];
          $project->name = $result['name'];
          $project->date = $result['date'];
          $project->structure = $result['structure'];
          $project->user_id = $result['user_id'];
          $project->project_type_id = $result['project_type_id'];
          $project->save();
         }
       }
       DB::commit();
      } catch (Exception $e) {
         return response()->json($e,400);
      }
      return response()->json('Task Complete',200);
    }
}
