<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Medication;
use Validator;
use App\Http\Resources\Medication as MedicationResource;
use Illuminate\Validation\Rule;

class MedicationController extends BaseController
{
    protected $records_per_page;
    public function __construct(){
        $this->records_per_page = config('app.records_per_page');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request){
       $query = Medication::with('medicationCategory')->where('is_active', 1);
       // $query = Medication::where('is_active', 1);
       if($request->item_code){
            $query->where('item_code','like','%'.$request->item_code.'%');
       }

       if($request->title){
            $query->where('title','like','%'.$request->title.'%');
       }

       if ($request->category_id) {
           $query->where('category_id',$request->category_id);
       }

       if ($request->wt_commision) {
           $query->where('wt_commision',$request->wt_commision);
       }

       if ($request->price) {
           $query->where('price',$request->price);
       }

       if ($request->unit_price) {
           $query->where('unit_price',$request->unit_price);
       }

       if (isset($request->add_commision)) {
           $query->where('add_commision',$request->add_commision);
       }

       $query->orderBy('id', 'DESC');
        // \DB::enableQueryLog();
       $medications = $query->paginate($this->records_per_page);
       // dd(\DB::getQueryLog()); 
        return $this->sendResponse($medications, ' Medications  retrieved successfully.');
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
            'item_code' => 'required|unique:medications,item_code',
            'title' => 'required',
            'category_id' => 'required',
            'unit_price' => 'required|numeric',
            'add_commision'=>'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);
        }

        $price = 0;
        if($input['add_commision'] == "1"){
            $commision = ($input['unit_price'] * 10) /100;
            $price = $input['unit_price'] + $commision;
        }
        if($input['add_commision'] == "0"){
            $price = $input['unit_price'];
        }

        $medication = new Medication();
        $medication->item_code = $input['item_code'];
        $medication->title = $input['title'];
        $medication->category_id = $input['category_id'];
        $medication->price = $price;
        $medication->unit_price = $input['unit_price'];
        $medication->add_commision = ($input['add_commision'])?$input['add_commision']:0;
        $medication->save();

        return $this->sendResponse(new MedicationResource($medication), 'Medication  created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $medication = Medication::find($id);

        if (is_null($medication)) {
            return $this->sendError('Medication not found.');
        }

        return $this->sendResponse(new MedicationResource($medication), 'Medication retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Medication $medication)
    {
        // echo "here";exit;
        $input = $request->all();

        $validator = Validator::make($input, [
            'item_code'=> [
                'required',
                Rule::unique('medications')->where(function ($query) use($medication) {
                    return $query->where('id','!=', $medication->id);
                }),
            ],
            'title' => 'required',
            'category_id' => 'required',
            'unit_price' => 'required|numeric',
            'add_commision'=>'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);   
        }

        $price = 0;
        if($input['add_commision'] == "1"){
            $commision = $input['unit_price'] * 10 /100;
            $price = $input['unit_price'] + $commision;
        }
        if($input['add_commision'] == "0"){
            $price = $input['unit_price'];
        }
        
        $medication->item_code = $input['item_code'];
        $medication->title = $input['title'];
        $medication->category_id = $input['category_id'];
        $medication->price = $price;
        $medication->unit_price = $input['unit_price'];
        $medication->add_commision = ($input['add_commision'])?$input['add_commision']:0;
        $medication->update();

        return $this->sendResponse(new MedicationResource($medication), 'Medication updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medication $medication){
        //$medication->delete();
        $medication->is_active = 0;
        $medication->save();
        return $this->sendResponse([], 'Medication deleted successfully.');
    }
}