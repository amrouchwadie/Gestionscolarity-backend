<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librarys;
use Validator;
use Illuminate\Routing\UrlGenerator;
use File;
use Mail;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    protected $librarys;
    protected $base_url;
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->base_url = $urlGenerator->to("/");
        $this->librarys = new Librarys;
    }

    public function addBook(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
 
            "book_name"=>"required|string",
            "description"=>"required|string",
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
        


        $file = $request->document;
        $file_nam = "";
        if($file==null)
        {
            $file_nam = "default-avatar.png";
        }else{
            $generate_nam = uniqid()."_".time().date("Ymd")."_FILE";
         $base64file =  $file;
         $fileBin2 = file_get_contents($base64file);
         $mimetype = mime_content_type($base64file); 
        
        $file_nam = $generate_nam.".pdf";
             
 
        }

        $this->librarys->figure_book = $file_nam;
        $this->librarys->book_name = $request->book_name;
        $this->librarys->domain = $request->domain;
        $this->librarys->author = $request->author;
        $this->librarys->publication_date = $request->publication_date;
        $this->librarys->description = $request->description;
        $this->librarys->photo = $file_name;
        $this->librarys->nbr_page = $request->nbr_page;
        $this->librarys->save();
        
      if($profile_picture == null )
      {
 
      }else{
          file_put_contents("./profile_images/".$file_name,$fileBin);

      }
      if($file == null )
      {
 
      }else{
          file_put_contents("./profile_images/".$file_nam,$fileBin2);

      }
      return response()->json([
           "success"=>true,
           "message"=>"Book saved successfully"
      ],200);
    }
    public function countBook() {
        $userCountBook = $this->librarys::count();
        return $userCountBook;
    }
    public function getPaginatedData($pagination=null)
    {
        $file_directory = $this->base_url."/profile_images";

        if($pagination==null || $pagination=="")
        {
            $librarys = $this->librarys->get()->toArray();
 
            return response()->json([
             "success"=>true,
             "data"=>$librarys,
             "file_directory"=>$file_directory

        ],200);
        }
 
        $librarys_paginated = $this->librarys->paginate($pagination);
        return response()->json([
         "success"=>true,
         "data"=>$librarys_paginated,
         "file_directory"=>$file_directory
    ],200);
    }
    
 
 
    public function editSingleData(Request $request,$id)
    {
       $validator = Validator::make($request->all(),
       [
        "book_name"=>"required|string",
        "domain"=>"required|string",

       ]);
 
       
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
       


       $file = $request->document;
       $file_nam = "";
       if($file==null)
       {
           $file_nam = "default-avatar.png";
       }else{
           $generate_nam = uniqid()."_".time().date("Ymd")."_FILE";
        $base64file =  $file;
        $fileBin2 = file_get_contents($base64file);
        $mimetype = mime_content_type($base64file); 
       
       $file_nam = $generate_nam.".pdf";
            

       }
 
       
       $findData->book_name = $request->book_name;
       $findData->domain = $request->domain;
       $findData->author = $request->author;
       $findData->figure_book = $file_nam;
       $findData->nbr_page = $request->nbr_page;
       $findData->publication_date = $request->publication_date;
       $findData->description = $request->description;
       $findData->photo = $file_name;
       $findData->save();
       if($profile_picture == null )
      {
 
      }else{
          file_put_contents("./profile_images/".$file_name,$fileBin);

      }
      if($file == null )
      {
 
      }else{
          file_put_contents("./profile_images/".$file_nam,$fileBin2);

      }
   
        return response()->json([
             "success"=>true,
             "message"=>"Book updated successfully",
        ],200);
     
 
    }
 
 
 
    public function deleteBooks($id)
    {
        $findData = $this->librarys::find($id);
        if(!$findData)
        {
            
        return response()->json([
         "success"=>true,
         "message"=>"Book with this id doesnt exist"
    ],500);
        }
 
        
        
        if($findData->delete())
        {
            $getFile == "default-avatar.png"? :File::delete("profile_images/".$getFile);
            
        return response()->json([
         "success"=>true,
         "message"=>"book deleted successfully"
    ],200);
        }
    }
    public function getSingleData($id)
    {
        $file_directory = $this->base_url."/profile_images";
     $findData = $this->librarys::find($id);
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
 
 
    public function searchData($search,$pagination=null)
    {
     if($pagination==null || $pagination=="")
     {
         $non_paginated_search_query = $this->librarys::where(function($query) use ($search){
          $query->where("book_name","LIKE","%$search%")->orWhere("nbr_page","LIKE","%$search%")->
          orWhere("domain","LIKE","%$search%")->orWhere("author","LIKE","%$search%")
          ->orWhere("publication_date","LIKE","%$search%");
         })->orderBy("id","DESC")->get()->toArray();
         return response()->json([
             "success"=>true,
             "data"=>$non_paginated_search_query,
        ],200);
     }
 
     $paginated_search_query = $this->librarys::where(function($query) use ($search){
        $query->where("book_name","LIKE","%$search%")->orWhere("nbr_page","LIKE","%$search%")->
        orWhere("domain","LIKE","%$search%")->orWhere("author","LIKE","%$search%")
        ->orWhere("publication_date","LIKE","%$search%");
         })->orderBy("id","DESC")->paginate($pagination);
         return response()->json([
             "success"=>true,
             "data"=>$paginated_search_query,
        ],200);
 
    }
 
  
public function create(){

}


 }