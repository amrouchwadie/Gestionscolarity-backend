<?php

use Illuminate\Http\Request;


    Route::post('register','User\AuthController@register');
    Route::post('login','User\AuthController@login');
    
    Route::post('professor/add','User\ProfessorController@addprofessor');
    Route::get('professor/get-all/{pagination?}','User\ProfessorController@getPaginatedData');
    Route::post('professor/update/{id}','User\ProfessorController@editSingleData');
    Route::post('professor/delete/{id}','User\ProfessorController@deleteProfessors');
    Route::get('professor/get-single/{id}','User\ProfessorController@getSingleData');
    Route::get('professor/search/{search}/{pagination?}','User\ProfessorController@searchData');
    
    Route::post('document/add','User\DocumentController@adddocument');
    Route::get('document/get-all/{id}/{pagination?}','User\DocumentController@getPaginatedData');
    Route::post('document/update/{id}','User\DocumentController@editSingleData');
    Route::post('document/delete/{id}','User\DocumentController@deleteDocuments');
    Route::get('document/get-single/{id}','User\DocumentController@getSingleData');
    Route::get('document/search/{search}/{id}/{pagination?}','User\DocumentController@searchData');

    Route::post('director/add','User\DirectorController@adddirector');
    Route::get('director/get-all/{pagination?}','User\DirectorController@getPaginatedDatadirector');
    Route::post('director/update/{id}','User\DirectorController@editSingleData');
    Route::post('director/delete/{id}','User\DirectorController@deleteDirectors');
    Route::get('director/get-single/{id}','User\DirectorController@getSingleData');
    Route::get('director/search/{search}/{pagination?}','User\DirectorController@searchDatadirector');
    Route::post('director/changepassword/{id}','User\DirectorController@changepassword');
    Route::get('loadProfile/{token}','User\DirectorController@loadProfile');
    Route::post('director/updateProfile/{id}','User\DirectorController@updateProfile');

    Route::post('book/add','User\LibraryController@addBook');
    Route::get('book/get-all/{pagination?}','User\LibraryController@getPaginatedData');
     Route::post('book/update/{id}','User\LibraryController@editSingleData');
     Route::post('book/delete/{id}','User\LibraryController@deleteBooks');
     Route::get('book/get-single/{id}','User\LibraryController@getSingleData');
     Route::get('book/search/{search}/{pagination?}','User\LibraryController@searchData');

     Route::post("filiere/add","User\FilieresController@addFiliere");
Route::get("filiere/get-all/{pagination?}","User\FilieresController@getPaginatedData");
Route::get("indexfiliere","User\FilieresController@index");

Route::post("filiere/update/{id}","User\FilieresController@editSingleData");
Route::post("filiere/delete/{id}","User\FilieresController@deleteFiliere");
Route::get("filiere/get-single/{id}","User\FilieresController@getSingleData");
Route::get("filiere/search/{search}/{pagination?}","User\FilieresController@searchData");

Route::post("niveau/add","User\NiveauController@addNiveau");
Route::get("niveau/get-all/{id}/{pagination?}","User\NiveauController@getPaginatedData");
Route::post("niveau/update/{id}","User\NiveauController@editSingleData");
Route::post("niveau/delete/{id}","User\NiveauController@deleteNiveau");
Route::get("niveau/get-single/{id}","User\NiveauController@getSingleData");
Route::get("niveau/search/{search}/{id}/{pagination?}","User\NiveauController@searchData");
Route::get("niveau/searchall/{search}/{pagination?}","User\NiveauController@searchallData");
Route::get("niveau/get-alldata/{pagination?}","User\NiveauController@getallData");


Route::get("getniveau/{id}","User\NiveauController@getniveau");


Route::get("indexniveau","User\NiveauController@indexniveau");

Route::get("getnameNiveau/{id}","User\NiveauController@getnameNiveau");

Route::post("module/add","User\ModuleController@addModule");
Route::get("module/get-all/{id}/{pagination?}","User\ModuleController@getPaginatedData");
Route::post("module/update/{id}","User\ModuleController@editSingleData");
Route::post("module/delete/{id}","User\ModuleController@deleteModule");
Route::get("module/get-single/{id}","User\ModuleController@getSingleData");
Route::get("module/search/{search}/{id}/{pagination?}","User\ModuleController@searchData");
Route::get("module/get-alldata/{pagination?}","User\ModuleController@getallData");
Route::get("module/searchall/{search}/{pagination?}","User\ModuleController@searchallData");
Route::get("store/{id}","User\ModuleController@indexmodule");

Route::post('student/add','User\StudentController@addstudent');
Route::get('student/get-all/{pagination?}','User\StudentController@getPaginatedData');
Route::post('student/update/{id}','User\StudentController@editSingleData');
Route::post('student/delete/{id}','User\StudentController@deleteStudents');
Route::get('student/get-single/{id}','User\StudentController@getSingleData');
Route::get('student/search/{search}/{pagination?}','User\StudentController@searchData');
Route::get("getnamefiliere/{id}","User\StudentController@getnamefiliere");
Route::get("getnameyear/{id}","User\StudentController@getnameyear");
Route::get("getprofile/{id}","User\StudentController@getprofile");
Route::get("getexactnamefiliere/{id}","User\StudentController@getexactnamefiliere");
Route::get("getexactnameniveau/{id}","User\StudentController@getexactnameniveau");
Route::get("getfiliere/{id}","User\StudentController@getfiliere");
Route::get('student/filtre/{filtre}/{pagination?}','User\StudentController@getPaginatedDatafiltre');
Route::get('loadProfilestudent/{token}','User\StudentController@loadProfile');


Route::post('course/add','User\CourseController@addCourse');
Route::get('course/get-all/{id}/{pagination?}','User\CourseController@getPaginatedData');
Route::post('course/update/{id}','User\CourseController@editSingleData');
Route::post('course/delete/{id}','User\CourseController@deleteCourse');
Route::get('course/get-single/{id}','User\CourseController@getSingleData');
Route::get('course/search/{search}/{pagination?}','User\CourseController@searchData');
Route::get("getnamemodule/{id}","User\CourseController@getnamemodule");
Route::get("getfilierename/{id}","User\CourseController@getfiliere");
Route::get("getnamelevel/{id}","User\CourseController@getnameyear");

Route::get('course/searchDataprofessor/{search}/{token}/{pagination?}','User\CourseController@searchDataprofessor');

Route::get('course/getprofessorData/{token}/{pagination?}','User\CourseController@getprofessorData');


Route::get("assign/getnamemodule/{id}","User\AssignController@getnamemodule");
Route::get("assign/getfilierename/{id}","User\AssignController@getfiliere");
Route::get("assign/getnamelevel/{id}","User\AssignController@getnameyear");
Route::post('assign/add','User\AssignController@addAssign');
Route::post('assign/update/{id}','User\AssignController@editSingleData');
Route::post('assign/delete/{id}','User\AssignController@deleteAssign');
Route::get('assign/get-single/{id}','User\AssignController@getSingleData');
Route::get('assign/search/{search}/{pagination?}','User\AssignController@searchData');

Route::get('assign/searchDataprofessor/{search}/{token}/{pagination?}','User\AssignController@searchDataprofessor');

Route::get('assign/getprofessorData/{token}/{pagination?}','User\AssignController@getprofessorData');


Route::post('DocumentsDuty/add','User\DocumentDutyController@adddocument');
Route::get('DocumentsDuty/get-all/{id}/{pagination?}','User\DocumentDutyController@getPaginatedData');
Route::post('DocumentsDuty/update/{id}','User\DocumentDutyController@editSingleData');
Route::post('DocumentsDuty/delete/{id}','User\DocumentDutyController@deleteDocuments');
Route::get('DocumentsDuty/get-single/{id}','User\DocumentDutyController@getSingleData');
Route::get('DocumentsDuty/search/{search}/{id}/{pagination?}','User\DocumentDutyController@searchData');




Route::get('countDocumentduty','User\DocumentDutyController@countDocumentduty');
Route::get('countCourse','User\DocumentController@countCourse');
Route::get('countAssign','User\FilieresController@countAssign');
Route::get('countProfessor','User\ProfessorController@countProfessor');
Route::get('countFiliere','User\FilieresController@countFiliere');
Route::get('countNiveau','User\NiveauController@countNiveau');
Route::get("countStudent","User\StudentController@countStudent");
Route::get('countBook','User\LibraryController@countBook');
Route::get('countDocument','User\DocumentController@countDocument');


Route::get('course/get-all/{token}/{pagination?}','User\CourseController@getPaginatedData');

Route::get('assign/get-all/{token}/{pagination?}','User\AssignController@getPaginatedData');



Route::post('DocumentsDutyanswer/addanswer','User\AnswerassignController@addanswer');
Route::post('DocumentsDutyanswer/update/{id}','User\AnswerassignController@editSingleData');
Route::post('DocumentsDutyanswer/delete/{id}','User\AnswerassignController@deletedocumentsanswer');
Route::get('DocumentsDutyanswer/get-single/{id}','User\AnswerassignController@getSingleData');
Route::get('DocumentsDutyanswer/get_data/{id}/{token}','User\AnswerassignController@getPaginatedData');


