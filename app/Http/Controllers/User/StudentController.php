<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Students;
use App\Niveaus;
use App\Modules;
use App\Filieres;
use App\User;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class StudentController extends Controller
{
    protected $user;
    protected $filiere;
    protected $modules;
    protected $niveaus;
    protected $students;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->students = new Students;
        $this->user = new User; 
        $this->niveaus = new Niveaus;
        $this->filiere = new Filieres;
        $this->modules = new Modules;


    }
 
    public function addstudent(Request $request)
 {
    $validator = Validator::make($request->all(),
    [
      
        "firstname_std"=>"required|string",
        "lastname_std"=>"required|string",
 

    ]
    );

    if($validator->fails())
    {
        return response()->json([
            "success"=>false,
            "message"=>$validator->messages()->toArray()
        ],500);
    }

    $profile_picture = $request->profile_image;
    $file_name = "";
    if($profile_picture==null)
    {
        $file_name = "default-avatar.png";
    }else{
        $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
     $base64Image =  $profile_picture;
     $fileBin = file_get_contents($base64Image);
     $mimetype = mime_content_type($base64Image); 
    
     if("image/png"==$mimetype)
     {
         $file_name = $generate_name.".png";
     } 
     else if("image/jpeg"==$mimetype)
     {
         $file_name = $generate_name.".jpeg";
     }
     else if("image/jpg"==$mimetype)
     {
         $file_name = $generate_name."jpg";
     }
    
      else{

        return response()->json([
            "success"=>false,
            "message"=>"only png ,jpg and jpeg files are accepted for setting profile pictures"
        ],500);
     }         

    }
    
   $this->students->code_std = $request->code_std;
   $this->students->firstname_std = $request->firstname_std;
   $this->students->lastname_std = $request->lastname_std;
   $this->students->telephone_std = $request->telephone_std;
   $this->students->email_std = $request->email_std;
   $this->students->code_filiere =  $request->code_filiere;
   $this->students->code_niveau = $request->code_niveau;
   $this->students->photo_std = $file_name; 
   $this->students->save();
   $this->user->type = "student";
   $this->user->email= $request->email_std;
   $this->user->password = Hash::make($request->password);
   $this->user->save();
  
  if($profile_picture == null)
  {

  }else{
      file_put_contents("./profile_images/".$file_name,$fileBin);
  }

  return response()->json([
       "success"=>true,
       "message"=>"Student saved successfully"
  ],200);
 }

 public function getnamefiliere($id)
 {
    $findData = $this->filiere::find($id);
          return $findData;
 }
 
 public function getnameyear($id)
 {
    $findData = $this->students::find($id);
    $data=$findData->code_niveau;
    $findData2 = $this->niveaus::find($data);
          return $findData2;
 }
 public function getnamemodule($id)
 {
    $findData = $this->students::find($id);
    $data=$findData->code_module;
    $findData2 = $this->modules::find($data);
          return $findData2;
 }

 public function getfiliere($id)
 {
    $findData = $this->students::find($id);
    $data=$findData->code_filiere;
    $findData2 = $this->filiere::find($data);
          return $findData2;
 }
    public function getPaginatedData($pagination=null)
    {
        $file_directory = $this->base_url."/profile_images";
        if($pagination==null || $pagination=="")
        {
            $students = $this->students->get()->toArray();
            return response()->json([
             "success"=>true,
             "data"=>$students,
             "file_directory"=>$file_directory
        ],200);
        }
 
        $students_paginated = $this->students->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$students_paginated,
         "file_directory"=>$file_directory
    ],200);
    }
    
    public function getPaginatedDatafiltre($filtre,$pagination=null)
    {
     $file_directory = $this->base_url."/profile_images";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->students::where(function($query) use ($filtre){
          $query->where("code_filiere","LIKE","%$filtre%")->orWhere("code_niveau","LIKE","%$filtre%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
     }
 
     $paginated_search_query = $this->students::where(function($query) use ($filtre){
          $query->where("code_filiere","LIKE","%$filtre%")->orWhere("code_niveau","LIKE","%$filtre%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
 
    }
    
    public function getprofile($id){
        $file_directory = $this->base_url."/profile_images";
        $findData = $this->students::find($id);
        $id_filiere = $findData->code_filiere;
        $id_niveau = $findData->code_niveau;
        $findData2 = $this->filiere::find($id_filiere);
        $findData3 = $this->niveaus::find($id_niveau);
        return response()->json([
            "success"=>true,
            "data"=>$findData,$findData2,$findData3,
            "file_directory"=>$file_directory
       ],200);
    }
    public function getexactnamefiliere($id){
        $findData = $this->students::find($id);
        $id_filiere = $findData->code_filiere;
        $findData2 = $this->filiere::find($id_filiere);
        
        return $findData2;
       
    }
    public function getexactnameniveau($id){
        $findData = $this->students::find($id);
        $id_niveau = $findData->code_niveau;
        $findData3 = $this->niveaus::find($id_niveau);  
        return $findData3;
    }
    
 
    public function download(Request $request,$id){
        $findData = $this->students::find($id);

        $file_directory = $this->base_url."/profile_images";
         $image=$findData->photo_std;
         $data=$file_directory + "/" +$image;
        return Storage::download($data);

    }







    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
           
        "firstname_std"=>"required|string",
        "lastname_std"=>"required|string",
        "telephone_std"=>"required|string",
       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->students::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
        $getFile = $findData->photo_std;
       
 
       $getFile=="default-avatar.png"? :File::delete('profile_images/'.$getFile);
    
 
        $profile_picture = $request->profile_image;
        
        
 
       $file_name = "";
       if($profile_picture==null)
       {
           $file_name = "default-avatar.png";
       }else{
           $generate_name = uniqid()."_".time().date("Ymd")."_IMG";
        $base64Image =  $profile_picture;
        $fileBin = file_get_contents($base64Image);
        $mimetype = mime_content_type($base64Image); 
        if("image/png"==$mimetype)
        {
            $file_name = $generate_name.".png";
        } 
        else if("image/jpeg"==$mimetype)
        {
            $file_name = $generate_name.".jpeg";
        }
        else if("image/jpg"==$mimetype)
        {
            $file_name = $generate_name."jpg";
        }
         else{
           return response()->json([
               "success"=>false,
               "message"=>"only png ,jpg and jpeg files are accepted for setting profile pictures"
           ],500);
        }
        
      }      
        $findData->firstname_std = $request->firstname_std;
        $findData->lastname_std = $request->lastname_std;
        $findData->telephone_std = $request->telephone_std;
        $findData->code_filiere = $request->code_filiere;
        $findData->code_niveau = $request->code_niveau;

        $findData->photo_std = $file_name; 

        $findData->save();
        if($profile_picture == null)
        {
   
        }else{
            file_put_contents("./profile_images/".$file_name,$fileBin);
        }
   
        return response()->json([
             "success"=>true,
             "message"=>"student updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteStudents($id)
    {
        
        $findData = $this->students::find($id);
        $data=$findData->email_std;
        $findData2 = $this->user::where("email",$data)->first();
        $findData2->delete();
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"student with this id doesnt exist"
    ],500);
        }
 
        
        
        if($findData->delete())
        {
            $getFile == "default-avatar.png"? :File::delete("profile_images/".$getFile);
            
        return response()->json([
         "success"=>true,
         "message"=>"student deleted successfully"
    ],200);
        }
    }
    public function countStudent() {
        $userCount = $this->students::count();
        return $userCount;
    }
    public function loadProfile($token)
    {
           $file_directory = $this->base_url."/profile_images";
           $user = auth("users")->authenticate($token);
           $user_id=$user->id;
           $user_email = $user->email;              
               $students = $this->students->where("email_std",$user_email)->first();    
               return response()->json([
                "success"=>true,
                "data"=>$students,
                "file_directory"=>$file_directory
           ],200);
    }
    public function getSingleData($id)
    {
        $file_directory = $this->base_url."/profile_images";
     $findData = $this->students::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"student with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
         "file_directory"=>$file_directory
    ],200);
    }
 
    public function searchData($search,$pagination=null)
    {
     $file_directory = $this->base_url."/profile_images";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->students::where(function($query) use ($search){
          $query->where("firstname_std","LIKE","%$search%")->orWhere("lastname_std","LIKE","%$search%")->
          orWhere("code_filiere","LIKE","%$search%")->orWhere("code_std","LIKE","%$search%")-> orWhere("code_niveau","LIKE","%$search%")->
          orWhere("email_std","LIKE","%$search%")->orWhere("telephone_std","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
     }
 
     $paginated_search_query = $this->students::where(function($query) use ($search){
          $query->where("firstname_std","LIKE","%$search%")->orWhere("lastname_std","LIKE","%$search%")->
          orWhere("code_filiere","LIKE","%$search%")->orWhere("code_std","LIKE","%$search%")-> orWhere("code_niveau","LIKE","%$search%")
          ->orWhere("email_std","LIKE","%$search%")->orWhere("telephone_std","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
 
    }
 


 }