<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\MedicationCategory;
use Validator;
use App\Http\Resources\MedicationCategory as CategoryResource;
use Illuminate\Validation\Rule;

class MedicationCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $records_per_page;
    public function __construct(){
        $this->records_per_page = config('app.records_per_page');
    }

    public function index(Request $request){
       $query = MedicationCategory::where('is_active', 1);
       if($request->category_name){
            $query->where('category_name','like','%'.$request->category_name.'%');
       }

       if ($request->wt_commision) {
           $query->where('wt_commision',$request->wt_commision);
       }

       $query->orderBy('id', 'DESC');

       $categories = $query->paginate($this->records_per_page);
     
        return $this->sendResponse($categories, ' Medication Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'category_name' => 'required|unique:medication_categories,category_name',
            'wt_commision' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);
        }

        $category = MedicationCategory::create($input);

        return $this->sendResponse(new CategoryResource($category), 'Medication Category created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $category = MedicationCategory::find($id);

        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }

        return $this->sendResponse(new CategoryResource($category), 'Medication Category retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $input = $request->all();

        $mc=MedicationCategory::find($id);
          
        $validator = Validator::make($input, [
            'category_name'=> [
                'required',
                Rule::unique('medication_categories')->where(function ($query) use($mc) {
                    return $query->where('id','!=', $mc->id);
                }),
            ],
            'wt_commision' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200); 
        }

        $mc->category_name = $input['category_name'];
        $mc->wt_commision = $input['wt_commision'];
        $mc->update();

        return $this->sendResponse(new CategoryResource($mc), 'Medication Category updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicationCategory $medicationCategory){
        //$medicationCategory->delete();
        $medicationCategory->is_active = 0;
        $medicationCategory->save();
        return $this->sendResponse([], 'Medication Category deleted successfully.');
    }
}