<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Subscription;
use Validator;
use App\Http\Resources\Subscription as SubscriptionResource;

class SubscriptionController extends BaseController
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
    public function index(Request $request)
    {
    
        $query = Subscription::where('is_active', 1);

        if($request->tier_name){
            $query->where('tier_name','like','%'.$request->tier_name.'%');
        }

        if($request->description){
            $query->where('description','like','%'.$request->description.'%');
        }

        if($request->subscription_fees){
            $query->where('subscription_fees',$request->subscription_fees);
        }
        
        $query->orderBy('id', 'DESC');

        $subscriptions = $query->paginate($this->records_per_page);
        
        return $this->sendResponse($subscriptions, 'Subscriptions retrieved successfully.');
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
            'tier_name' => 'required',
            'subscription_fees' => 'required',
            'description' => 'required',
            'delivery_distance_from' => 'required',
            'delivery_distance_to' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);      
        }

        $subscription = Subscription::create($input);

        return $this->sendResponse(new SubscriptionResource($subscription), 'Subscription created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = Subscription::find($id);

        if (is_null($subscription)) {
            return $this->sendError('Subscription not found.');
        }

        return $this->sendResponse(new SubscriptionResource($subscription), 'Subscription retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'tier_name' => 'required',
            'subscription_fees' => 'required',
            'description' => 'required',
            'delivery_distance_from' => 'required',
            'delivery_distance_to' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),200);    
        }

        $subscription->tier_name = $input['tier_name'];
        $subscription->subscription_fees = $input['subscription_fees'];
        $subscription->description = $input['description'];
        $subscription->delivery_distance_from = $input['delivery_distance_from'];
        $subscription->delivery_distance_to = $input['delivery_distance_to'];
        $subscription->save();

        return $this->sendResponse(new SubscriptionResource($subscription), 'Product updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription){
         // $subscription->delete();
        $subscription->is_active = 0;
        $subscription->save();
        return $this->sendResponse([], 'Subscription deleted successfully.');
    }

}