<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Courses;
use App\Students;
use App\Filieres;
use App\Niveaus;
use App\Modules;
use App\Professors;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;

class CourseController extends Controller
{
    protected $professors;
    protected $module;
    protected $filieres;
    protected $niveaus;
    protected $courses;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->middleware("auth:users");
        $this->base_url = $urlGenerator->to("/");
        $this->courses = new Courses;
        $this->professors = new Professors;
        $this->module = new Modules;
        $this->filieres = new Filieres;
        $this->niveaus = new Niveaus;


    }
    public function countCourse() {
        $userCount = $this->courses::count();
        return $userCount;
    }
    public function addCourse(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            "token"=>"required",
            "title_course"=>"required|string",
            ]
        );
 
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "message"=>$validator->messages()->toArray()
            ],500);
        }
        $user_token = $request->token;
        $user = auth("users")->authenticate($user_token);
        $user_email = $user->email;
        $findData = $this->professors::where("email",$user_email)->first();
        $data=$findData->id;
        $this->courses->professor_id = $data;
        $this->courses->title_course = $request->title_course;
        $this->courses->visibility_course = $request->visibility_course;
        $this->courses->code_niveau = $request->code_niveau;
        $this->courses->code_filiere = $request->code_filiere;
        $this->courses->code_module = $request->code_module;
        $this->courses->save();
     
 
      return response()->json([
           "success"=>true,
           "message"=>"Course saved successfully"
      ],200);
    }


    public function getPaginatedData($token,$pagination=null)
    {
        $user = auth("users")->authenticate($token);
        $user_email = $user->email;
        $findData = Students::where("email_std",$user_email)->first();
        $data=$findData->code_niveau;
        $visibility="null";
        if($pagination==null || $pagination=="")
        {
            $courses = $this->courses->where("visibility_course",$visibility)->
            orWhere("code_niveau",$data)->orderBy("id","DESC")->get()->toArray();
            
            return response()->json([
             "success"=>true,
             "data"=>$courses,
            ],400);
        }
        $courses_paginated = $this->courses->where("visibility_course",$visibility)->
        orWhere("code_niveau",$data)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$courses_paginated,

    ],400);
    }
 
   

  public function getprofessorData($token,$pagination=null)
    { 
        $user = auth("users")->authenticate($token);
        $user_email = $user->email;
        $findData = $this->professors::where("email",$user_email)->first();
        $data=$findData->id;
        $visibility="null";
        if($pagination==null || $pagination=="")
        {
            $courses = $this->courses->where("professor_id",$data)->orderBy("id","DESC")->get()->toArray();
            
            return response()->json([
             "success"=>true,
             "data"=>$courses
            ],400);
        }
         
        $courses_paginated = $this->courses->where("professor_id",$data)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$courses_paginated
    ],400);
    }
    public function getnameyear($id)
 {
    $findData = $this->courses::find($id);
    $data=$findData->code_niveau;
    $findData2 = $this->niveaus::find($data);
          return $findData2;
 }
 public function getnamemodule($id)
 {
    $findData = $this->courses::find($id);
    $data=$findData->code_module;
    $findData2 = $this->modules::find($data);
          return $findData2;
 }

 public function getfiliere($id)
 {
    $findData = $this->courses::find($id);
    $data=$findData->code_filiere;
    $findData2 = $this->filiere::find($data);
          return $findData2;
 }

    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
        "title_course"=>"required|string",
       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->courses::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       $findData->title_course = $request->title_course;
       $findData->code_filiere = $request->code_filiere;
       $findData->code_niveau = $request->code_niveau;
       $findData->code_module = $request->code_module;
       $findData->visibility_course = $request->visibility_course;
        $findData->save();
   
        return response()->json([
             "success"=>true,
             "message"=>"Courses updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteCourse($id)
    {
        $findData = $this->courses::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Course with this id doesnt exist"
    ],500);
        }
 
        if($findData->delete())
        {            
        return response()->json([
         "success"=>true,
         "message"=>"Course deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
     $findData = $this->courses::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"Course with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
    ],200);
    }
 
    public function searchData($search,$id,$pagination=null)
    {
     $visibility="null";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->courses::where("visibility_course",$visibility)->
         orWhere("code_niveau",$id)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_course","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->courses::where("visibility_course",$visibility)->
     orWhere("code_niveau",$id)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_course","LIKE","%$search%")
          ;
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
    public function searchDataprofessor($search,$token,$pagination=null)
    {
        $user = auth("users")->authenticate($token);
        $user_email = $user->email;
        $findData = $this->professors::where("email",$user_email)->first();
        $id_professor=$findData->id;
     $visibility="null";
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->courses::where("visibility_course",$visibility)->
         orWhere("professor_id",$id_professor)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_course","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }

     $paginated_search_query = $this->courses::where("visibility_course",$visibility)->
     orWhere("professor_id",$id_professor)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_course","LIKE","%$search%")
          ;
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
 }