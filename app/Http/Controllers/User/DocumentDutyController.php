<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DocumentsDuty;
use App\User;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Maatwebsite\Excel\Facades\Excel;


class DocumentDutyController extends Controller
{
    protected $user;
    protected $documentsDuty;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->documentsDuty = new DocumentsDuty;
        $this->user = new User; 

    }
 
    public function adddocument(Request $request)
 {
    $validator = Validator::make($request->all(),
    [

        "description_doc"=>"required|string",
        "name_doc"=>"required|string" 

    ]
    );

    if($validator->fails())
    {
        return response()->json([
            "success"=>false,
            "message"=>$validator->messages()->toArray()
        ],500);
    }

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
   $this->documentsDuty->description_doc = $request->description_doc;
   $this->documentsDuty->name_doc = $request->name_doc;
   $this->documentsDuty->assign_id = $request->assign_id;
   $this->documentsDuty->path_doc = $file_name;
   $this->documentsDuty->save();
  
  
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



 public function countDocumentduty() {
    $Documentduty = $this->documentsDuty::count();
    return $Documentduty;
}
    public function getPaginatedData($id,$pagination=null)
    {
        $file_directory = $this->base_url."/profile_images";
        if($pagination==null || $pagination=="")
        {
            $documentsDuty = $this->documentsDuty->where("assign_id",$id)->orderBy("id","DESC")->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$documentsDuty,
             "file_directory"=>$file_directory
        ],200);
        }
 
        $documentsDuty_paginated = $this->documentsDuty->where("assign_id",$id)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$documentsDuty_paginated,
         "file_directory"=>$file_directory
    ],200);
    }
 
 
 
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
           
        "description_doc"=>"required|string",
        "name_doc"=>"required|string" 

       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->documentsDuty::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
        $getFile = $findData->photo;
       
 
       $getFile=="default-avatar.png"? :File::delete('profile_file/'.$getFile);
    
 
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
       
         else{
           return response()->json([
               "success"=>false,
               "message"=>"only pdf and docx files are accepted for setting file"
           ],500);
        }
        
      }
 
        
        $findData->name_doc = $request->name_doc;
        $findData->description_doc = $request->description_doc;
        $findData->path_doc = $file_name;
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
 
 
 
    public function deletedocumentsDuty($id)
    {
        $findData = $this->documentsDuty::find($id);
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
        $file_directory = $this->base_url."/profile_file";
     $findData = $this->documentsDuty::find($id);
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
 
    public function searchData($search,$id,$pagination=null)
    {
     $file_directory = $this->base_url."/profile_images";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->documentsDuty::where("assign_id",$id)->where(function($query) use ($search){
          $query->where("description_doc","LIKE","%$search%")->orWhere("name_doc","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
     }
 
     $paginated_search_query = $this->documentsDuty::where("assign_id",$id)->where(function($query) use ($search){
        $query->where("description_doc","LIKE","%$search%")->orWhere("name_doc","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
 
    }
 



 }