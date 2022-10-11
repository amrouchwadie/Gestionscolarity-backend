<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Answerassign;
use App\User;
use App\Students;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Maatwebsite\Excel\Facades\Excel;


class AnswerassignController extends Controller
{
    protected $user;
    protected $answerassign;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->answerassign = new Answerassign;
        $this->user = new User; 

    }
 
    public function addanswer(Request $request)
 {
    $profile_file = $request->document;
    $file_name = "";
    if($profile_file==null)
    {
        $file_name = "default-avatar.png";
    }else{
        $generate_name = uniqid()."_".time().date("Ymd")."_FILE";
     $base64Image =  $profile_file;
     $fileBin = file_get_contents($base64Image);
     $mimetype = mime_content_type($base64Image); 
    
     if("application/pdf"==$mimetype)
     {
         $file_name = $generate_name.".pdf";
     } 
     else if("application/vnd.openxmlformats-officedocument.wordprocessingml.document"==$mimetype)
     {
         $file_name = $generate_name.".docx";
     }
     else if("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"==$mimetype)
     {
         $file_name = $generate_name.".xlsx";
     }
     else if("application/vnd.ms-excel.sheet.macroEnabled.12"==$mimetype)
     {
         $file_name = $generate_name.".xlsm";
     }
     else if("application/vnd.openxmlformats-officedocument.presentationml.presentation"==$mimetype)
     {
         $file_name = $generate_name.".pptx";
     }
     else if("application/vnd.ms-powerpoint"==$mimetype)
     {
         $file_name = $generate_name.".ppt";
     }
      else{

        return response()->json([
            "success"=>false,
            "message"=>"only pdf and docx  files are accepted for setting file"
        ],500);
     }         

    }
    $user_token = $request->token;
    $user = auth("users")->authenticate($user_token);
    $user_email = $user->email;
    $findData = Students::where("email_std",$user_email)->first();
    $data=$findData->id;
    $data2=$findData->firstname_std+" "+$findData->lastname_std;
    $this->answerassign->student_id = $data;
    $this->answerassign->student_name = $data2;
    $this->answerassign->name_doc = $request->name_doc;
    $this->answerassign->assign_id = $request->assign_id;
    $this->answerassign->chifre_answer = $file_name;
    $this->answerassign->save();
  
  
  if($profile_file == null)
  {

  }else{
      file_put_contents("./profile_images/".$file_name,$fileBin);
  }

  return response()->json([
       "success"=>true,
       "message"=>"Document saved successfully"
  ],200);
 }




 public function getPaginatedData($id,$token)
    {
        $user = auth("users")->authenticate($token);
        $user_email = $user->email;
        $findData = Students::where("email_std",$user_email)->first();
        $data=$findData->id;
            $file_directory = $this->base_url."/profile_images";
            $docanswer = $this->answerassign->where("student_id",$data)->
            Where("assign_id",$id)->orderBy("id","DESC")->get()->toArray();
            return response()->json([
             "success"=>true,
             "data"=>$docanswer,
             "file_directory"=>$file_directory

            ],400);
    }


    public function editSingleData(Request $request,$id)
    {
       $findData = $this->answerassign::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
        $getFile = $findData->chifre_answer;
       $getFile=="default-avatar.png"? :File::delete('profile_images/'.$getFile);
    
 
       $profile_file = $request->document;
       $file_name = "";
       if($profile_file==null)
       {
           $file_name = "default-avatar.png";
       }else{
           $generate_name = uniqid()."_".time().date("Ymd")."_FILE";
        $base64Image =  $profile_file;
        $fileBin = file_get_contents($base64Image);
        $mimetype = mime_content_type($base64Image); 
       
        if("application/pdf"==$mimetype)
        {
            $file_name = $generate_name.".pdf";
        } 
        else if("application/vnd.openxmlformats-officedocument.wordprocessingml.document"==$mimetype)
        {
            $file_name = $generate_name.".docx";
        }
        else if("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"==$mimetype)
        {
            $file_name = $generate_name.".xlsx";
        }
        else if("application/vnd.ms-excel.sheet.macroEnabled.12"==$mimetype)
        {
            $file_name = $generate_name.".xlsm";
        }
        else if("application/vnd.openxmlformats-officedocument.presentationml.presentation"==$mimetype)
        {
            $file_name = $generate_name.".pptx";
        }
        else if("application/vnd.ms-powerpoint"==$mimetype)
        {
            $file_name = $generate_name.".ppt";
        }
         else{
   
           return response()->json([
               "success"=>false,
               "message"=>"only pdf,excel,powerpoint and docx  files are accepted for setting file"
           ],500);
        }         
   
       }
        $findData->chifre_answer = $file_name;
        $findData->save();
        if($profile_file == null)
        {
   
        }else{
            file_put_contents("./profile_images/".$file_name,$fileBin);
        }
   
        return response()->json([
             "success"=>true,
             "message"=>"document updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deletedocumentsanswer($id)
    {
        $findData = $this->answerassign::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Document with this id doesnt exist"
    ],500);
        }
 
        
        
        if($findData->delete())
        {
            $getFile == "default-avatar.png"? :File::delete("profile_images/".$getFile);
            
        return response()->json([
         "success"=>true,
         "message"=>"document deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
        $file_directory = $this->base_url."/profile_images";
     $findData = $this->answerassign::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"document with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
         "file_directory"=>$file_directory
    ],200);
    }
 }