<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filieres;
use App\Assign;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;

class FilieresController extends Controller
{
    protected $filieres;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->filieres = new Filieres;
    }
 
    public function addFiliere(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
 
            "name_filiere"=>"required|string",
            "designation_filiere"=>"required|string"
                    ]
        );
 
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "message"=>$validator->messages()->toArray()
            ],500);
        }
       
       $this->filieres->name_filiere = $request->name_filiere;
       $this->filieres->designation_filiere = $request->designation_filiere;
       $this->filieres->save();
     
 
      return response()->json([
           "success"=>true,
           "message"=>"Filiere saved successfully"
      ],200);
    }
    public function getPaginatedData($pagination=null)
    {
        
        if($pagination==null || $pagination=="")
        {
            $filieres = $this->filieres->get()->toArray();
            
            return response()->json([
             "success"=>true,
             "data"=>$filieres
            ],400);
        }
         
        $filieres_paginated = $this->filieres->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$filieres_paginated
    ],400);
    }
 
    public function countFiliere() {
        $userCountfiliere = $this->filieres::count();
        return $userCountfiliere;
    }

    public function countAssign() {
        $userCountAssign = Assign::count();
        return $userCountAssign;
    }
    public function index()
    {
        $result = Filieres::paginate();
           return $result;
    }
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
        "name_filiere"=>"required|string",
        "designation_filiere"=>"required|string"
       ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->filieres::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
       $findData->name_filiere = $request->name_filiere;
       $findData->designation_filiere = $request->designation_filiere;
        $findData->save();
   
        return response()->json([
             "success"=>true,
             "message"=>"Filiere updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteFiliere($id)
    {
        $findData = $this->filieres::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Filiere with this id doesnt exist"
    ],500);
        }
 
        if($findData->delete())
        {            
        return response()->json([
         "success"=>true,
         "message"=>"Filiere deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
     $findData = $this->filieres::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"Filiere with this id doesnt exist"
 ],500);
     }
     return response()->json([
         "success"=>true,
         "data"=>$findData,
    ],200);
    }
 
    public function searchData($search,$pagination=null)
    {
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->filieres::where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("designation_filiere","LIKE","%$search%")
          ->orWhere("name_filiere","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->filieres::where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")->orWhere("designation_filiere","LIKE","%$search%")
          ->orWhere("name_filiere","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
 
 }