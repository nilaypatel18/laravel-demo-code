<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Order;
use App\OrderItem;
use App\Address;
use App\UserSubscription;
use App\Medication;
use Validator;
use App\Http\Resources\Order as OrderResource;

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
        
        $query = \DB::table('orders')->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
         ->leftJoin('addresses', 'addresses.id', '=', 'orders.address_id')
         ->leftJoin('leads', 'leads.id', '=', 'orders.lead_id')
         ->where('orders.is_active','1');

        if($request->order_date){
            $query->where('orders.created_at','like','%'.$request->order_date.'%');
        }

        if($request->name){
            $query->where('leads.name','like','%'.$request->name.'%');
        }

        if($request->mobile){
            $query->where('leads.mobile',$request->mobile);
        }

        if($request->email){
            $query->where('leads.email','like','%'.$request->email.'%');
        }

        if($request->status){
            $query->where('orders.order_status',$request->order_status);
        }

        if($request->source){
            $query->where('leads.source',$request->source);
        } 

        if($request->opharma_reference){
            $query->where('orders.opharma_reference','like','%'.$request->opharma_reference.'%');
        }  

        if($request->delivery_type){
            $query->where('orders.delivery_type',$request->delivery_type);
        }               
        \DB::enableQueryLog();
        // $orders = $query->paginate($this->records_per_page);    
        $orders = $query->get();    
        // echo "<pre>";print_r($orders);echo "</pre>";
        dd(\DB::getQueryLog());
        return $this->sendResponse(OrderResource::collection($orders), 'Orders retrieved successfully.');
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
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            // 'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );

        $address = Address::create($addressData);
        $order->address_id = $address->id;
        
        $order->delivery_type = $input['delivery_type']; 
        $order->delivery_charge = $input['delivery_charge']; 
        $order->total_cost = $basket_value + $input['delivery_charge']; 
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
        $order = Order::find($id);

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
                    'is_active'=>1,
                );

        // $order = Order::update($data);
        $order->fill($data)->save();

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

            $orderitem = OrderItem::where('order_id',$order->id)->first();
            // echo "<pre>";print_r($order);echo "</pre>";exit;
            $orderitem->fill($orderItemData)->save();
        }

        $addressData = array(
            'address_line1'=>$input['address_line1'],
            'address_line2'=>$input['address_line2'],
            'city'=>$input['city'],
            // 'state'=>$input['state'],
            'country_id'=>$input['country_id'],
            'postal_code'=>$input['postal_code'],
        );

        $address = Address::find($order->address_id);
        $address->fill($addressData)->save();
        
        $order->delivery_type = $input['delivery_type']; 
        $order->delivery_charge = $input['delivery_charge']; 
        $order->total_cost = $basket_value + $input['delivery_charge']; 
        $order->order_status = 1;

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

    

   
}