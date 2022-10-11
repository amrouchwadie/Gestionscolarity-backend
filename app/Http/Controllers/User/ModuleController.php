<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;

class ModuleController extends Controller
{
    protected $modules;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->modules = new Modules;
    }
 
    public function addModule(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
 
            "name_module"=>"required|string",
            
                    ]
        );
 
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "message"=>$validator->messages()->toArray()
            ],500);
        }
       
      $this->modules->name_module = $request->name_module;
       $this->modules->niveau_id = $request->niveau_id;
       $this->modules->save();
     
 
      return response()->json([
           "success"=>true,
           "message"=>"Module saved successfully"
      ],200);
    }


    public function indexmodule($id)
    {
        $result =  $this->modules::where("niveau_id",$id)->orderBy("id","DESC")->paginate();
           return $result;
    }
    public function getnamemodule($id)
    {
        $result =  $this->modules::find($id);
           return $result;
    }



    public function getPaginatedData($id,$pagination=null)
    {
        if($pagination==null || $pagination=="")
        {
            $modules = $this->modules->where("niveau_id",$id)->orderBy("id","DESC")->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$modules,
        ],400);
        }
 
        $modules_paginated = $this->modules->where("niveau_id",$id)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$modules_paginated,
    ],400);
    }
 
 
 
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
            "name_module"=>"required|string"     
              ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->modules::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
       $findData->name_module = $request->name_module;
        $findData->save();
   
        return response()->json([
             "success"=>true,
             "message"=>"Name Module updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteModule($id)
    {
        $findData = $this->modules::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Module with this id doesnt exist"
    ],500);
        }
 
        if($findData->delete())
        {            
        return response()->json([
         "success"=>true,
         "message"=>"Module deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
     $findData = $this->modules::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"Module with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
    ],200);
    }
  
    public function searchData($search,$id,$pagination=null)
    {
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->modules::where("niveau_id",$id)->
         where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_module","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->modules::where("niveau_id",$id)->
     where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_module","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
    public function getallData($pagination=null)
    {
        if($pagination==null || $pagination=="")
        {
            $modules = $this->modules->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$modules,
        ],400);
        }
 
        $modules_paginated = $this->modules->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$modules_paginated,
    ],400);
    }
    public function searchallData($search,$pagination=null)
    {
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->modules::
         where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_module","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->modules::
     where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_module","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
 }