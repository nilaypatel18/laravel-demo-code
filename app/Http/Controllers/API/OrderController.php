<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Order;
use App\OrderItem;
use App\Address;
use App\UserSubscription;
use App\Medication;
use App\B2b;
use App\Lead;
use Validator;
use App\Http\Resources\Order as OrderResource;
use Barryvdh\DomPDF\Facade as PDF;

class OrderController extends BaseController
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
        
        // $query = \DB::table('orders')->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
        //  ->leftJoin('addresses', 'addresses.id', '=', 'orders.address_id')
        //  ->leftJoin('leads', 'leads.id', '=', 'orders.lead_id')
        //  ->where('orders.is_active','1');

                   
        // \DB::enableQueryLog();
        // $orders = $query->paginate($this->records_per_page);    
        // $orders = $query->get();    
        // echo "<pre>";print_r($orders);echo "</pre>";
        // dd(\DB::getQueryLog());

        $query = Order::with('orderitem','address','lead')->where('is_active', 1)->whereHas('lead', function($q) use ($request){
            // $q->where('gender', 'Male');
        if($request->name){
            $q->where('leads.name','like','%'.$request->name.'%');
        }

        if($request->mobile){
            $q->where('leads.mobile',$request->mobile);
        }

        if($request->email){
            $q->where('leads.email','like','%'.$request->email.'%');
        }
        
        if($request->lead_id){
            $q->where('leads.id',$request->lead_id);
        }

        });

        if($request->source){
            $query->where('orders.source',$request->source);
        }  

        if($request->order_date){
            $query->whereDate('orders.created_at',$request->order_date);
        }
        
        if($request->status){
            $query->where('orders.order_status',$request->status);
        }

        if($request->opharma_reference){
            $query->where('orders.opharma_reference','like','%'.$request->opharma_reference.'%');
        }  

        if($request->delivery_type){
            $query->where('orders.delivery_type',$request->delivery_type);
        }    
        
        $query->orderBy('id', 'DESC')->get();

        $orders =$query->paginate($this->records_per_page);

        return $this->sendResponse($orders, 'Orders retrieved successfully.');
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
            'lead_id' => 'required',
            'source' => 'required',
            'opharma_reference' => 'required',
            'order_date' => 'required',
            // 'prescription_file'=> 'required',
            // 'prescription_file_url'=> 'required',
            'delivery_type' => 'required',
            'delivery_charge' => 'required',
            'channel'=>'required',
            'created_by'=>'required',
            'source'=>'required',
            'shipping_name'=>'required',
            'shipping_email'=>'required',
            'shipping_mobile'=>'required',
            'shipping_phone'=>'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = array(
                    'lead_id' => $input['lead_id'],
                    'source' => $input['source'],
                    'opharma_reference' => isset($input['opharma_reference'])?$input['opharma_reference']:'',
                    'order_date'=>$input['order_date'],
                    'prescription_file'=>'',
                    'prescription_file_url'=>isset($input['prescription_file_url'])?$input['prescription_file_url']:'',
                    'delivery_type'=>$input['delivery_type'],
                    'delivery_charge'=>$input['delivery_charge'],
                    'channel'=>$input['channel'],
                    'created_by'=>$input['created_by'],
                    'source'=>$input['source'],
                    'is_active'=>1,
                );

        $order = Order::create($data);

        $basket_value =0;
        for($i=0;$i<count($input['medications']);$i++){

            $medication = Medication::find($input['medications'][$i]['id']);
            $orderItemData = array(
                'order_id'=>$order->id,
                'medication_id'=>$input['medications'][$i]['id'],
                'unit_price'=>$medication->price,
                'quantity'=>$input['medications'][$i]['quantity'],
                'subtotal'=>$input['medications'][$i]['quantity'] * $medication->price,
            ); 

            $basket_value = $basket_value + $orderItemData['subtotal'];
            $orderItem = OrderItem::create($orderItemData);
        }

        $addressData = array(
            'name'=>$input['shipping_name'],
            'email'=>$input['shipping_email'],
            'mobile'=>$input['shipping_mobile'],
            'phone'=>$input['shipping_phone'],
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            // 'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );

        $address = Address::create($addressData);
        $order->address_id = $address->id;

        $lead = Lead::find($input['lead_id']);
        $organizaion_discount = 0;
        if(isset($lead->organization_id)){
            $organization = B2b::find($lead->organization_id);
            if(isset($organization)){
                $organizaion_discount = $organization->discount;
            }
        }

        $organization_discount_cost =0;
        if($organizaion_discount != 0){
            $total_cost = $basket_value + $input['delivery_charge'];
            $organization_discount_cost = ($basket_value + $input['delivery_charge']) * $organizaion_discount / 100; 
            $final_cost = $total_cost - $organization_discount_cost; 
        }else{
            $final_cost = $basket_value + $input['delivery_charge']; 
        }
        
        $order->delivery_type = $input['delivery_type']; 
        $order->delivery_charge = $input['delivery_charge']; 
        $order->basket_value = $basket_value; 
        $order->total_cost = $final_cost; 
        $order->organization_discount = $organizaion_discount; 
        $order->organization_discount_cost = $organization_discount_cost; 
        $order->order_status = 1;

        $order->save();

        return $this->sendResponse(new OrderResource($order), 'Order created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $order = Order::with('orderitem','address','lead')->where('id',$id)->first();

        if (is_null($order)) {
            return $this->sendError('Order not found.');
        }

        return $this->sendResponse(new OrderResource($order), 'Order retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Order $order)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'lead_id' => 'required',
            'source' => 'required',
            'opharma_reference' => 'required',
            'order_date' => 'required',
            // 'prescription_file'=> 'required',
            // 'prescription_file_url'=> 'required',
            'delivery_type' => 'required',
            'delivery_charge' => 'required',
            'channel'=>'required',
            'created_by'=>'required',
            'source'=>'required',
            'shipping_name'=>'required',
            'shipping_email'=>'required',
            'shipping_mobile'=>'required',
            'shipping_phone'=>'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = array(
                    'lead_id' => $input['lead_id'],
                    'source' => $input['source'],
                    'opharma_reference' => isset($input['opharma_reference'])?$input['opharma_reference']:'',
                    'order_date'=>$input['order_date'],
                    'prescription_file'=>'',
                    'prescription_file_url'=>isset($input['prescription_file_url'])?$input['prescription_file_url']:'',
                    'delivery_type'=>$input['delivery_type'],
                    'delivery_charge'=>$input['delivery_charge'],
                    'channel'=>$input['channel'],
                    'source'=>$input['source'],
                    'created_by'=>$input['created_by'],
                    'is_active'=>1,
                );

        // $order = Order::update($data);
        $order->fill($data)->save();

        OrderItem::where('order_id',$order->id)->delete();

        $basket_value =0;
        for($i=0;$i<count($input['medications']);$i++){

            $medication = Medication::find($input['medications'][$i]['id']);
            $orderItemData = array(
                'order_id'=>$order->id,
                'medication_id'=>$input['medications'][$i]['id'],
                'unit_price'=>$medication->price,
                'quantity'=>$input['medications'][$i]['quantity'],
                'subtotal'=>$input['medications'][$i]['quantity'] * $medication->price,
            ); 

            $basket_value = $basket_value + $orderItemData['subtotal'];

            OrderItem::create($orderItemData);
        }

        $addressData = array(
            'name'=>$input['shipping_name'],
            'email'=>$input['shipping_email'],
            'mobile'=>$input['shipping_mobile'],
            'phone'=>$input['shipping_phone'],
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            // 'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );
      
        $address = Address::find($order->address_id);
        // echo "<pre>";print_r($address);echo "</pre>";exit;
        $address->fill($addressData)->save();

        $lead = Lead::find($input['lead_id']);
        $organizaion_discount = 0;
        if(isset($lead->organization_id)){
            $organization = B2b::find($lead->organization_id);
            if(isset($organization)){
                $organizaion_discount = $organization->discount;
            }
        }

        $organization_discount_cost =0;
        if($organizaion_discount != 0){
            $total_cost = $basket_value + $input['delivery_charge'];
            $organization_discount_cost = ($basket_value + $input['delivery_charge']) * $organizaion_discount / 100; 
            $final_cost = $total_cost - $organization_discount_cost; 
        }else{
            $final_cost = $basket_value + $input['delivery_charge']; 
        }
        
        $order->delivery_type = $input['delivery_type']; 
        $order->delivery_charge = $input['delivery_charge']; 
        $order->basket_value = $basket_value; 
        $order->total_cost = $final_cost; 
        $order->organization_discount = $organizaion_discount; 
        $order->organization_discount_cost = $organization_discount_cost; 
        $order->order_status = $input['order_status'];

        $order->save();

        return $this->sendResponse(new OrderResource($order), 'Order updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order){
        // $lead->delete();
        $order->is_active = 0;
        $order->save();
        return $this->sendResponse([], 'Order deleted successfully.');
    }
    
    public function savepdf(Request $request){
        $order = Order::with('orderitem','address','lead')->where('id',$request->id)->first();
        $organization = array();
        if(isset($order->lead)){
            if(isset($order->lead->organization_id)){
                $organization = B2b::find($order->lead->organization_id);
            }
        }
        $pdf = PDF::loadView('pdfview',compact('order','organization'));
        \Storage::disk('s3')->put("order_" . $request->id . ".pdf", $pdf->output(), 'public');

        return $this->sendResponse(array('pdf_url'=>\Storage::disk('s3')->url("order_" . $request->id . ".pdf")), 'Pdf generated successfully.');
        
        // return view('pdfview',compact('order','organization'));
    }

    
    public function updateStatus(Request $request){
        $input = $request->all();
        if(isset($input['orders'])){
            for($i=0;$i<count($input['orders']);$i++){
                $order = Order::find($input['orders'][$i]['id']);
                $order->order_status = $input['orders'][$i]['status'];
                $order->save();
            }
        }
        
        return $this->sendResponse([], 'Order updated successfully.');
    }

    

   
}