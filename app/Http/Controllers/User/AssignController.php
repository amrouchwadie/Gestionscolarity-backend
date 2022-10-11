<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Assign;
use App\Modules;
use App\Professors;
use App\Students;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;

class AssignController extends Controller
{
    protected $professors;
    protected $module;
    protected $assigns;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->middleware("auth:users");
        $this->base_url = $urlGenerator->to("/");
        $this->assigns = new Assign;
        $this->professors = new Professors;
        $this->module = new Modules;


    }
 
    public function addAssign(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            "token"=>"required",
            "title_assign"=>"required|string",
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
        $this->assigns->professor_id = $data;
        $this->assigns->title_assign = $request->title_assign;
        $this->assigns->visibility_assign = $request->visibility_assign;
        $this->assigns->code_niveau = $request->code_niveau;
        $this->assigns->code_filiere = $request->code_filiere;
        $this->assigns->code_module = $request->code_module;
        $this->assigns->save();
     
 
      return response()->json([
           "success"=>true,
           "message"=>"Assign saved successfully"
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
            $assigns = $this->assigns->where("visibility_assign",$visibility)->
            orWhere("code_niveau",$data)->orderBy("id","DESC")->get()->toArray();
            
            return response()->json([
             "success"=>true,
             "data"=>$assigns
            ],400);
        }
         
        $assigns_paginated = $this->assigns->where("visibility_assign",$visibility)->
        orWhere("code_niveau",$data)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$assigns_paginated
    ],400);
    }
 
   

  public function getprofessorData($token,$pagination=null)
    { 
        $user = auth("users")->authenticate($token);
        $user_email = $user->email;
        $findData = $this->professors::where("email",$user_email)->first();
        $data=$findData->id;
        if($pagination==null || $pagination=="")
        {
            $assigns = $this->assigns->where("professor_id",$data)->orderBy("id","DESC")->get()->toArray();
            
            return response()->json([
             "success"=>true,
             "data"=>$assigns
            ],400);
        }
         
        $assigns_paginated = $this->assigns->where("professor_id",$data)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$assigns_paginated
    ],400);
    }
    public function getnameyear($id)
    {
       $findData = $this->assigns::find($id);
       $data=$findData->code_niveau;
       $findData2 = $this->niveaus::find($data);
             return $findData2;
    }
    
    public function getnamemodule($id)
    {
       $findData = $this->assigns::find($id);
       $data=$findData->code_module;
       $findData2 = $this->modules::find($data);
             return $findData2;
    }
   
    public function getfiliere($id)
    {
       $findData = $this->assigns::find($id);
       $data=$findData->code_filiere;
       $findData2 = $this->filiere::find($data);
             return $findData2;
    }
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
        "title_assign"=>"required|string",
       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->assigns::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       $findData->title_assign = $request->title_assign;
       $findData->code_filiere = $request->code_filiere;
       $findData->code_niveau = $request->code_niveau;
       $findData->code_module = $request->code_module;
       $findData->visibility_assign = $request->visibility_assign;
        $findData->save();
   
        return response()->json([
             "success"=>true,
             "message"=>"assigns updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteAssign($id)
    {
        $findData = $this->assigns::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Assign with this id doesnt exist"
    ],500);
        }
 
        if($findData->delete())
        {            
        return response()->json([
         "success"=>true,
         "message"=>"Assign deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
     $findData = $this->assigns::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"Assign with this id doesnt exist"
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
         $non_paginated_search_query = $this->assigns::where("visibility_assign",$visibility)->
         orWhere("code_niveau",$id)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_assign","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->assigns::where("visibility_assign",$visibility)->
     orWhere("code_niveau",$id)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_assign","LIKE","%$search%")
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
         $non_paginated_search_query = $this->assigns::where("visibility_assign",$visibility)->
         orWhere("professor_id",$id_professor)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_assign","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }

     $paginated_search_query = $this->assigns::where("visibility_assign",$visibility)->
     orWhere("professor_id",$id_professor)->orderBy("id","DESC")->where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("title_assign","LIKE","%$search%")
          ;
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
 }