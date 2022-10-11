<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Niveaus;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;

class NiveauController extends Controller
{
    protected $niveaus;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->niveaus = new Niveaus;
    }
 
    public function addNiveau(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
 
            "name_niveau"=>"required|string",
            
                    ]
        );
 
        if($validator->fails())
        {
            return response()->json([
                "success"=>false,
                "message"=>$validator->messages()->toArray()
            ],500);
        }
       
      $this->niveaus->name_niveau = $request->name_niveau;
       $this->niveaus->filiere_id = $request->filiere_id;
       $this->niveaus->save();
     
 
      return response()->json([
           "success"=>true,
           "message"=>"Niveau saved successfully"
      ],200);
    }
    public function getPaginatedData($id,$pagination=null)
    {
        if($pagination==null || $pagination=="")
        {
            $niveaus = $this->niveaus->where("filiere_id",$id)->orderBy("id","DESC")->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$niveaus,
        ],400);
        }
 
        $niveaus_paginated = $this->niveaus->where("filiere_id",$id)->orderBy("id","DESC")->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$niveaus_paginated,
    ],400);
    }
    public function countNiveau() {
        $userCountniveau = $this->niveaus::count();
        return $userCountniveau;
    }
    public function getniveau($id)
    {
        $result =  Niveaus::where("filiere_id",$id)->orderBy("id","DESC")->paginate();
           return $result;
    }
    public function indexniveau(Request $request)
    {
        $result = Niveaus::paginate();
           return $result;
    }

    public function getnameNiveau($id)
 {
    $findData = Niveaus::find($id);
          return $findData;
 }
 
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
            "name_niveau"=>"required|string"     
              ]);
 
       
       if($validator->fails())
       {
           return response()->json([
               "success"=>false,
               "message"=>$validator->messages()->toArray()
           ],500);
       }
 
    
       $findData = $this->niveaus::find($id);
       if(!$findData)
       {
         return response()->json([
             "success"=>false,
             "message"=>"please this content has no valid id"
         ],401);
       }
 
       
       $findData->name_niveau = $request->name_niveau;
        $findData->save();
   
        return response()->json([
             "success"=>true,
             "message"=>"Name niveau updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteNiveau($id)
    {
        $findData = $this->niveaus::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Niveau with this id doesnt exist"
    ],500);
        }
 
        if($findData->delete())
        {            
        return response()->json([
         "success"=>true,
         "message"=>"Niveau deleted successfully"
    ],200);
        }
    }
 
    public function getSingleData($id)
    {
     $findData = $this->niveaus::find($id);
     if(!$findData)
     {
         
     return response()->json([
      "success"=>true,
      "message"=>"Niveau with this id doesnt exist"
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
         $non_paginated_search_query = $this->niveaus::where("filiere_id",$id)->
         where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_niveau","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->niveaus::where("filiere_id",$id)->
     where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_niveau","LIKE","%$search%");
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
            $niveaus = $this->niveaus->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$niveaus,
        ],400);
        }
 
        $niveaus_paginated = $this->niveaus->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$niveaus_paginated,
    ],400);
    }
    public function searchallData($search,$pagination=null)
    {
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->niveaus::
         where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_niveau","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->niveaus::
     where(function($query) use ($search){
          $query->where("id","LIKE","%$search%")
          ->orWhere("name_niveau","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
 }