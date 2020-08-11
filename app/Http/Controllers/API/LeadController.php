<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Lead;
use App\Address;
use App\UserSubscription;
use Validator;
use App\Http\Resources\Lead as LeadResource;

class LeadController extends BaseController
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

        $query = Lead::with('address','b2b')->where('is_active', 1);

        if($request->name){
            $query->where('name','like','%'.$request->name.'%');
        }

        if($request->email){
            $query->where('email','like','%'.$request->email.'%');
        }

        if($request->mobile_no){
            $query->where('mobile_no',$request->mobile_no);
        }

        if(isset($request->status)){
            $query->where('leads_status',$request->status);
        } 

        if(isset($request->source)){
            $query->where('source',$request->source);
        } 
        
        $query->orderBy('id', 'DESC');

        $leads = $query->paginate($this->records_per_page);

        return $this->sendResponse($leads, 'Lead(s) retrieved successfully.');
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
            'name' => 'required',
            'email' => 'required',
            'mobile_no' => 'required',
            'phone' => 'required',
            // 'subscription_tier_id' => 'required',
            // 'subscription_fees' => 'required',
            'leads_status' => 'required',
            // 'lead_owner_id' => 'required',
            'comments' => 'required',
            // 'organization_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = array(
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'phone' => $input['phone'],
                    'mobile_no' => $input['mobile_no'],
                    'leads_status'=>$input['leads_status'],
                    // 'lead_owner_id'=>$input['lead_owner_id'],
                    'comments'=>$input['comments'],
                    'organization_id' => isset($input['organization_id'])?$input['organization_id']:0,
                    'is_active'=>1,
                );

        if($input['leads_status'] == 3){
            $data = $data + array('source' => $input['source']);
        } 

        $lead = Lead::create($data);

        $addressData = array(
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );

        $address = Address::create($addressData);
        $lead->address_id = $address->id;

        // $subscriptionData = array(
        //     'subscription_id'=>$input['subscription_tier_id'],
        //     'subscription_fees'=>$input['subscription_fees'],
        //     'is_active'=>1,
        // );
        // $userSubscription = UserSubscription::create($subscriptionData);
        // $lead->user_subscription_id = $userSubscription->id;

        $lead->save();

        return $this->sendResponse(new LeadResource($lead), 'Lead  created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $lead = Lead::with('b2b','address')->where('id',$id)->first();

        if (is_null($lead)) {
            return $this->sendError('Lead not found.');
        }

        return $this->sendResponse($lead, 'Lead retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Lead $lead)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'mobile_no' => 'required',
            'phone' => 'required',
            // 'subscription_tier_id' => 'required',
            // 'subscription_fees' => 'required',
            'leads_status' => 'required',
            // 'lead_owner_id' => 'required',
            'comments' => 'required',
            // 'organization_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $lead->name = $input['name'];
        $lead->email = $input['email'];
        $lead->mobile_no = $input['mobile_no'];
        $lead->phone = $input['phone'];
        $lead->leads_status = $input['leads_status'];
        $lead->organization_id = isset($input['organization_id'])?$input['organization_id']:0;
        if($input['leads_status'] == 3){
            $lead->source = $input['source'];    
        }
        // $lead->lead_owner_id = $input['lead_owner_id'];
        $lead->comments = $input['comments'];

        $addressData = array(
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );

        $address = Address::create($addressData);
        $lead->address_id = $address->id;

        // $subscriptionData = array(
        //     'subscription_id'=>$input['subscription_tier_id'],
        //     'subscription_fees'=>$input['subscription_fees'],
        //     'is_active'=>1,
        // );
        // $userSubscription = UserSubscription::create($subscriptionData);
        // $lead->user_subscription_id = $userSubscription->id;

        $lead->save();

        return $this->sendResponse(new LeadResource($lead), 'Lead updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead){
        // $lead->delete();
        $lead->is_active = 0;
        $lead->save();
        return $this->sendResponse([], 'Lead deleted successfully.');
    }

    public function addSubscriber(Request $request){
        $input = $request->all();
 
        $validator = Validator::make($input, [
            'lead_id' => 'required',
            'source' => 'required',
            'comments' => 'required',
            // 'is_active' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());    
        }

        $lead = Lead::find($input['lead_id']);
        $lead->comments=$input['comments'];
        $lead->source=$input['source'];
        $lead->leads_status = 3;
        $lead->save();

        return $this->sendResponse(new LeadResource($lead), 'Lead updated successfully.');
    }

    public function getAllSubscribers(){
        $leads = Lead::where('leads_status',3)->get();
        return $this->sendResponse(LeadResource::collection($leads), 'Subscribers retrieved successfully.');
    }



}