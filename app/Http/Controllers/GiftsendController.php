<?php

namespace App\Http\Controllers;

use App\Models\Giftsend;
use App\Models\User;
use App\Models\GiftcardsNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;
use Session;
use Validator;
use App\Mail\GeftcardMail;
use App\Mail\ResendGiftcard;
use App\Mail\GiftCardStatement;
use App\Mail\GiftcardCancelMail;
class GiftsendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
        $result =$this->postAPI('gift-for-other',$data);
        if(isset($result['error']))
        {
            session()->flash('msg', '<h5 style="color: red;">' . $result['error'] . '</h5>');
            return redirect('/register');
        }
        else{
            session()->flash('msg', '<h5 style="color: green;">' . $result['success'] . '</h5>');
            return redirect('/');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Giftsend  $giftsend
     * @return \Illuminate\Http\Response
     */
    public function show(Giftsend $giftsend)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Giftsend  $giftsend
     * @return \Illuminate\Http\Response
     */
    public function edit(Giftsend $giftsend)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Giftsend  $giftsend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Giftsend $giftsend)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Giftsend  $giftsend
     * @return \Illuminate\Http\Response
     */
    public function destroy(Giftsend $giftsend)
    {
        //
    }


    //  For Coupon Validate
    public function giftvalidate(Request $request){
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
        $result =$this->postAPI('coupon-validate',$data);
        if(isset($result['error'])) {
            echo json_encode(["error" => '<h5 style="color: red;">' . $result['error'] . '</h5>']);
        } else {
            echo json_encode(["success" => '<h5 style="color: green;">' . $result['success'] . '</h5>','data'=>$result['data']]);
        }
    }

    //  for giftcards  send

    public function sendgift(Request $request){
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
       
        //  First API
        $resultData =$this->postAPI('gift-for-other',$data);
        $result=json_decode($resultData['result']);
        $request->session()->put('gift_id', $result->id);

        if($result->discount!=0)
        {
            $discount_dispaly="<tr style='background-color:#FCA52A;'><th>Discount: </th><th>".'$'. ($result->discount)."</th></tr>";
        }
else{
    $discount_dispaly='';
}
        if(isset($resultData['error'])) {
            echo json_encode(["error" => '<h5 style="color: red;">' . $resultData['error'] . '</h5>']);
        } 
        else {
                //  for gift send to other
                if($result->recipient_name!=null)
                {
                  
                    echo json_encode([
                        "success" => '<h5 style="color: green;">' . $resultData['success'] . '</h5>',
                        "result" => '<table class="table table-striped">
                                       <tbody>
                                         <tr><th id="giftqty"></th>
                                         <th>$'.$result->amount.'</th></tr>
                                         <tr><th>Your name:</th><th>'.$result->your_name.'</th></tr>
                                         <tr><th>Recipient name:</th><th>'.$result->recipient_name.'</th></tr>
                                         <tr><th>Message:</th><th>'.$result->message.'</th></tr>
                                         <tr><th>Ship To:</th><th>'.$result->gift_send_to.'</th></tr>
                                         <tr><th>Receipt To:</th><th>'.$result->receipt_email.'</th></tr>'.$discount_dispaly.'
                                         <tr><th>Total:</th><th>'.'$'.$result->amount - ($result->discount ? $result->discount : 0).'</th></tr>
                                       </tbody>
                                     </table>',
                        "paymentscript" => '<script
                                             src="https://checkout.stripe.com/checkout.js"
                                             class="stripe-button"
                                             data-key="'.env('STRIPE_KEY').'"
                                             data-name="Forever Medspa"
                                             data-description="Forever Medspa Giftcards"
                                             data-amount="'.(($result->amount - ($result->discount ? $result->discount : 0)) * 100).'" // Convert to cents
                                             data-email="info@forevermedspanj.com"
                                             data-image="'.url('/medspa.png').'"
                                             data-currency="usd"
                                             id="stripeButton">
                                           </script>'
                    ]);  
                }   
        }
    }

    // For Self Giftcards
    public function selfgift(Request $request){
        $data_arr = $request->except('_token');
        
        $data = json_encode($data_arr);
        //  First API
        $resultData =$this->postAPI('gift-for-self',$data);
        $result=json_decode($resultData['result']);
        $request->session()->put('gift_id', $result->id);

        if($result->discount!=0)
        {
            $discount_dispaly="<tr style='background-color: #FCA52A;'><th>Discount: </th><th>".'$'. ($result->discount)."</th></tr>";
        }
else{
    $discount_dispaly='';
}
        if(isset($resultData['error'])) {
            echo json_encode(["error" => '<h5 style="color: red;">' . $resultData['error'] . '</h5>']);
        } else {
                        
            echo json_encode([
                "success" => '<h5 style="color: green;">' . $resultData['success'] . '</h5>',
                "result" => '<table class="table table-striped">
                               <tbody>
                                 <tr><th id="giftqty"></th><th>$'.$result->amount.'</th></tr>
                                 <tr><th>Your name:</th><th>'.$result->your_name.'</th></tr>
                                 <tr><th>Shipping By Email:</th><th>'.$result->receipt_email.'</th></tr>'.$discount_dispaly.'
                                 <tr><th>Total:</th><th>'.'$'.$result->amount - ($result->discount ? $result->discount : 0).'</th></tr>
                               </tbody>
                             </table>',
                "paymentscript" => '<script
                                     src="https://checkout.stripe.com/checkout.js"
                                     class="stripe-button"
                                     data-key="'.env('STRIPE_KEY').'"
                                     data-name="Forever Medspa"
                                     data-description="Forever Medspa Giftcards"
                                     data-amount="'.(($result->amount - ($result->discount ? $result->discount : 0)) * 100).'" // Convert to cents
                                     data-email="info@forevermedspanj.com"
                                     data-image="'.url('/medspa.png').'"
                                     data-currency="usd"
                                     id="stripeButton">
                                   </script>'
            ]); 
            
        }
    }

    // for balnce check of giftcards
    
        //  For Coupon Validate
        public function knowbalance(Request $request){
            $data_arr = $request->except('_token');
            $data = json_encode($data_arr);
            $result =$this->postAPI('giftcard-balance-check',$data);
     
            if(isset($result['status']) && $result['status']==200) {
                echo json_encode(["success" => '<h5 style="color: green;">' . $result['result'] . '</h5>']);
            } else {
                echo json_encode(["error" => '<h5 style="color: red;">' . $result['error'] . '</h5>']);
            }
        }

    //  for giftcard redeem
    public function giftcardredeemView(Request $request){
        $token= Auth::user()->user_token;
        $data_arr = ['name'=>'','email'=>'','giftcardnumber'=>'','user_token'=>$token];
        $data = json_encode($data_arr);
        $result = $this->postAPI('gift-card-search', $data);

        if (isset($result['status']) && $result['status'] == 200) {
            $getdata = $result['result'];
            return view('admin.redeem.redeem_view', compact('getdata'));
        } else {
            $error = isset($result['error']) ? $result['error'] : 'Unknown error occurred.';
            return view('admin.redeem.redeem_view')->with('error', $error);
        }
    }
    public function GiftCardSearch(Request $request)
    {
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
        $result = $this->postAPI('gift-card-search', $data);

        if (isset($result['status']) && $result['status'] == 200) {
            $getdata = $result['result'];
            return view('admin.redeem.redeem_view', compact('getdata'));
        } else {
            $error = isset($result['error']) ? $result['error'] : 'Unknown error occurred.';
            return view('admin.redeem.redeem_view')->with('error', $error);
        }
    }

    function giftcardredeem(Request $request){
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
        $result = $this->postAPI('gift-card-redeem', $data);
        if($result['status']==200){
            $data_arr = $request->except('_token','amount','comments','user_id');
            $data = json_encode($data_arr);
            $statement = $this->postAPI('gift-card-statment', $data);
            $statement['giftCardHolderDetails'] = $result['giftCardHolderDetails'];
            
            Mail::to($result['giftCardHolderDetails']['gift_send_to'])->send(new GiftCardStatement($statement));
        }

        return $result;

    }

    function giftcardstatment(Request $request){
        $data_arr = $request->except('_token');
        $data = json_encode($data_arr);
        $result = $this->postAPI('gift-card-statment', $data);
        return $result;

    }

    public function giftsale(){
        return view('gift.gift_sale');
    } 

     // For Self Giftcards
//      public function GiftPurchase(Request $request){
        
//         $data_arr = $request->except('_token');
//         $transaction_id='FEMS-'.time();
//         $data_arr['transaction_id']=$transaction_id;
//         $data_arr['payment_mode']='Form Forever Medspa Center';
//         $data = json_encode($data_arr);
//         //  First API
//         $resultData =$this->postAPI('gift-purchase-from-store',$data);
//         $result = (object) $resultData['result'];
//         return redirect()->route('giftcard-purchases-success')->with('transaction_details', $result);
//         // return view('gift.gift_purchase_payment_history')->with('result', $result);
    

// }

public function GiftPurchase(Request $request) {
    // Define validation rules
    $request->validate([
        'your_name' => 'required|string|max:255',
        'gift_send_to' => 'required|email|max:255',
    ], [
        'your_name.required' => 'The Your Name field is required.',
        'gift_send_to.required' => 'The email field is required.',
        'gift_send_to.email' => 'The email must be a valid email address.',
    ]);
    

    // If validation passes, proceed with the rest of the logic
    $data_arr = $request->except('_token');
    $transaction_id = 'FEMS-' . time();
    $data_arr['transaction_id'] = $transaction_id;
    $data_arr['payment_mode'] = 'From Forever Medspa Center';
    $data = json_encode($data_arr);
    // Call the API
    $resultData = $this->postAPI('gift-purchase-from-store', $data);
    $result = (object) $resultData['result'];

    return redirect()->route('giftcard-purchases-success')->with('transaction_details', $result);
}


public function GiftPurchaseSuccess()
{
    // Retrieve the 'result' data from the session
    $transactionDetails = session('transaction_details');
    if($transactionDetails)
    {
        // Pass the 'result' data to the view if needed
        return view('gift.gift_purchase_payment_history', ['result' => $transactionDetails]);
    }
    else{

        return redirect()->route('giftcards-sale');
    }
}

public function payment_confirmation(Request $request){
    $data_arr = $request->except('_token');
    $data_arr['transaction_amount']=Session::get('amount_medspa_center_purchase');
    // dd($data_arr);//transaction_amount
    $data = json_encode($data_arr);
    //  First API
    $resultData =$this->postAPI('payment_confirmation',$data);

    if($resultData['status']==200)
    {
        return redirect('admin/cardgenerated-list');
    }
    else
    {
        return redirect('admin/cardgenerated-list');
    }
 
}

// for giftcards list

public function cardgeneratedList(Request $request, User $user,GiftcardsNumbers $number,Giftsend $giftsend){
    $token = Auth::user()->user_token;
    $data_arr = ['user_token'=>$token];
    $data = json_encode($data_arr);
    $result =$this->postAPI('gift-list',$data);
    if(isset($result['status']) && $result['status']==200) {
        $data=$result['result'];
        return view('admin.cardnumber.index',compact('data'));
    } else {
        return view('admin.cardnumber.index')->with('error','Something Went');
        
    }
 
}
//  for payment status update
public function updatePaymentStatus(Request $request){
    $data_arr = $request->except('_token');
    $data = json_encode($data_arr);
    $result = $this->postAPI('payment_status_update', $data);
return $result;
}



public function giftcancel(Request $request,){
    $data_arr = $request->except('_token');
    
    $data = json_encode($data_arr);
    $result =$this->postAPI('giftcard-cancel',$data);
    if(isset($result['status']) && $result['status']==200) {
        
        $data_arr_status = ['gift_card_number' => $request->gift_card_number, 'user_token' => $request->user_token];
        $data = json_encode($data_arr_status);
        $statement = $this->postAPI('gift-card-statment', $data);
        $statement['receiverAndSenderDetails'] = $result['receiverAndSenderDetails'][0];

        if ($statement['receiverAndSenderDetails']['receipt_email'] != '') {
            $tomail = $statement['receiverAndSenderDetails']['receipt_email'];
        } else {
            $tomail = $statement['receiverAndSenderDetails']['gift_send_to'];
        }
        
        // Convert $tomail to string if it's an array
        $tomail = is_array($tomail) ? $tomail[0] : $tomail;
        
        Mail::to($tomail)->send(new GiftcardCancelMail($statement));
        

     } 
    return $result;
 
}

public function Resendmail_view(Request $request){
    $mail_data = Giftsend::findOrFail($request->id);
    return view('email.email_template_view',compact('mail_data'));

}

public function Resendmail(Request $request)
{
    try {
        $statement = $request->all();
        $statement['send_mail']='yes';
        Mail::to($statement['gift_send_to'])->cc($request->cc)->bcc($request->bcc)->send(new ResendGiftcard($statement));

        return back()->with('message', 'Email sent successfully.');
    } 
    catch (Exception $e) {
        return back()->with('error','Failed to send email.');
    }
}



}
