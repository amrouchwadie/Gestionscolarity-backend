<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Professors;
use App\User;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Maatwebsite\Excel\Facades\Excel;


class ProfessorController extends Controller
{
    protected $user;
    protected $professors;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->professors = new Professors;
        $this->user = new User; 

    }
 
    public function addprofessor(Request $request)
 {
    $validator = Validator::make($request->all(),
    [

        "firstname"=>"required|string",
        "lastname"=>"required|string",
        "telephone"=>"required|string",
        "email"=>"required|email",
        "type"=>"required|string",  
        "password"=>"required|string",  

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
   $this->professors->firstname = $request->firstname;
   $this->professors->lastname = $request->lastname;
   $this->professors->telephone = $request->telephone;
   $this->professors->email = $request->email;
   $this->professors->type = $request->type;
   $this->professors->photo = $file_name; 
   $this->professors->type_administration = "professor"; 
   $this->professors->save();
   $this->user->type = "professor";
   $this->user->email = $request->email;
   $this->user->password = Hash::make($request->password);
   $this->user->save();
  
  if($profile_picture == null)
  {

  }else{
      file_put_contents("./profile_images/".$file_name,$fileBin);
  }

  return response()->json([
       "success"=>true,
       "message"=>"Professor saved successfully"
  ],200);
 }

 public function export() 
    {
        $columns= $this->professors->getTableColumns();
        return Excel::download($this->professors, 'Professor.xlsx');
    }


 public function adddirector(Request $request)
 {
    $validator = Validator::make($request->all(),
    [

        "firstname"=>"required|string",
        "lastname"=>"required|string",
        "telephone"=>"required|string",
        "email"=>"required|email",
        "type"=>"required|string",  
        "password"=>"required|string",  

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
   $this->professors->firstname = $request->firstname;
   $this->professors->lastname = $request->lastname;
   $this->professors->telephone = $request->telephone;
   $this->professors->email = $request->email;
   $this->professors->type = $request->type;
   $this->professors->photo = $file_name; 
   $this->professors->type_administration = "admin"; 
   $this->professors->save();
   $this->user->type = "admin";
   $this->user->email = $request->email;
   $this->user->password = Hash::make($request->password);
   $this->user->save();
  
  if($profile_picture == null)
  {

  }else{
      file_put_contents("./profile_images/".$file_name,$fileBin);
  }

  return response()->json([
       "success"=>true,
       "message"=>"Director saved successfully"
  ],200);
 }
 public function countProfessor() {
    $typead="professor";
    $userCountprof = $this->professors::where("type_administration",$typead)->count();
    return $userCountprof;
}

    public function getPaginatedData($pagination=null)
    {
        $typead="professor";
        $file_directory = $this->base_url."/profile_images";
        if($pagination==null || $pagination=="")
        {
            $professors = $this->professors->where("type_administration",$typead)->orderBy("id","DESC")->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$professors,
             "file_directory"=>$file_directory
        ],200);
        }
 
        $professors_paginated = $this->professors->where("type_administration",$typead)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$professors_paginated,
         "file_directory"=>$file_directory
    ],200);
    }
 
 
 
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
           
        "firstname"=>"required|string",
        "lastname"=>"required|string",
        "telephone"=>"required|string",
       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->professors::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
        $getFile = $findData->photo;
       
 
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
 
        
        $findData->telephone = $request->telephone;
        $findData->firstname = $request->firstname;
        $findData->lastname = $request->lastname;
        $findData->photo = $file_name; 
        $findData->save();
        if($profile_picture == null)
        {
   
        }else{
            file_put_contents("./profile_images/".$file_name,$fileBin);
        }
   
        return response()->json([
             "success"=>true,
             "message"=>"professor updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteProfessors($id)
    {
        $findData = $this->professors::find($id);
        $data=$findData->email_std;
        $findData2 = $this->user::where("email",$data)->first();
        $findData2->delete();
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"professor with this id doesnt exist"
    ],500);
        }
 
        
        
        if($findData->delete())
        {
            $getFile == "default-avatar.png"? :File::delete("profile_images/".$getFile);
            
        return response()->json([
         "success"=>true,
         "message"=>"professor deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
        $file_directory = $this->base_url."/profile_images";
     $findData = $this->professors::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"professor with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
         "file_directory"=>$file_directory
    ],200);
    }
 
    //this function is to search for data as well as paginating our data searched
    public function searchData($search,$pagination=null)
    {
     $file_directory = $this->base_url."/profile_images";
     $typead="professor";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->professors::where("type_administration",$typead)->where(function($query) use ($search){
          $query->where("firstname","LIKE","%$search%")->orWhere("lastname","LIKE","%$search%")->
          orWhere("type","LIKE","%$search%")->orWhere("email","LIKE","%$search%")->orWhere("telephone","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
     }
 
     $paginated_search_query = $this->professors::where("type_administration",$typead)->where(function($query) use ($search){
          $query->where("firstname","LIKE","%$search%")->orWhere("lastname","LIKE","%$search%")->
          orWhere("type","LIKE","%$search%")->orWhere("email","LIKE","%$search%")->orWhere("telephone","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
 
    }
 




    public function searchDatadirector($search,$pagination=null)
    {
     $file_directory = $this->base_url."/profile_images";
     $typead="admin";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->professors::where("type_administration",$typead)->where(function($query) use ($search){
          $query->where("firstname","LIKE","%$search%")->orWhere("lastname","LIKE","%$search%")->
          orWhere("type","LIKE","%$search%")->orWhere("email","LIKE","%$search%")->orWhere("telephone","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
     }
 
     $paginated_search_query = $this->professors::where("type_administration",$typead)->where(function($query) use ($search){
          $query->where("firstname","LIKE","%$search%")->orWhere("lastname","LIKE","%$search%")->
          orWhere("type","LIKE","%$search%")->orWhere("email","LIKE","%$search%")->orWhere("telephone","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
             "file_directory"=>$file_directory
        ],200);
 
    }
 
    public function getPaginatedDatadirector($pagination=null)
    {
        $typead="admin";
        $file_directory = $this->base_url."/profile_images";
        if($pagination==null || $pagination=="")
        {
            $professors = $this->professors->where("type_administration",$typead)->orderBy("id","DESC")->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$professors,
             "file_directory"=>$file_directory
        ],200);
        }
 
        $professors_paginated = $this->professors->where("type_administration",$typead)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$professors_paginated,
         "file_directory"=>$file_directory
    ],200);
    }
 
 }