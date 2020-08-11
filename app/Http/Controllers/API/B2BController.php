<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\B2b;
use Validator;
use App\Http\Resources\B2b as B2bResource;
use Illuminate\Validation\Rule;

class B2BController extends BaseController
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
       $query = B2b::where('is_active', 1);

       $b2b = $query->paginate($this->records_per_page);
     
        return $this->sendResponse($b2b, ' B2b retrieved successfully.');
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
            'company_name' => 'required|unique:b2b,company_name',
            'discount' => 'required',
            'delivery_value' => 'required',
            'note' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);
        }

        $b2b = B2b::create($input);

        return $this->sendResponse(new B2bResource($b2b), 'B2b created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $b2b = B2b::find($id);

        if (is_null($b2b)) {
            return $this->sendError('b2b not found.');
        }

        return $this->sendResponse(new B2bResource($b2b), 'B2b retrieved successfully.');
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

        $b2b=B2b::find($id);
          
        $validator = Validator::make($input, [
            'company_name' => 'required',
            'discount' => 'required',
            'delivery_value' => 'required',
            'note' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);      
        }

        $b2b->company_name = $input['company_name'];
        $b2b->discount = $input['discount'];
        $b2b->delivery_value = $input['delivery_value'];
        $b2b->note = $input['note'];
        $b2b->update();

        return $this->sendResponse(new B2bResource($b2b), 'B2b updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(B2b $b2b){
        $b2b->delete();
        return $this->sendResponse([], 'B2b deleted successfully.');
    }
}