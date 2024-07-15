<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Search_keyword;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Auth;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $token= Auth::user()->user_token;
        $data_arr = ['user_token'=>$token];
        $data = json_encode($data_arr);
        $data = $this->postAPI('product-list', $data);
  
        return view('admin.product.product_index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $token= Auth::user()->user_token;
        $data_arr = ['user_token'=>$token];
        $categorydata = json_encode($data_arr);

        $categoryresult = $this->postAPI('category-list',$categorydata);
        $category=$categoryresult['result'];
      
        
        return view('admin.product.product_create',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Product $product)
    {
        $token = Auth::user()->user_token;

    // Retrieve all request data except '_token'
    $data = $request->except('_token');

    // Add the user's token to the data
    $data['user_token'] = $token;
    $data['cat_id']=implode('|',$request->cat_id);
    if ($request->hasFile('product_image')) {
        $folder = str_replace(" ", "_", $token);
        $image = $request->file('product_image');
        $destinationPath = '/uploads/' . $folder."/";
        $filename = $image->getClientOriginalName();
        $image->move(public_path($destinationPath), $filename);
        $data['product_image'] = url('/').$destinationPath.$filename;
    }
 
    // Send data to API endpoint using cURL
    $curl = curl_init();
  
    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => env('API_URL') . 'product-created', // API URL
        CURLOPT_RETURNTRANSFER => true, // Return the response as a string
        CURLOPT_ENCODING => '', // Enable compression
        CURLOPT_MAXREDIRS => 10, // Follow up to 10 redirects
        CURLOPT_TIMEOUT => 0, // No timeout
        CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version
        CURLOPT_CUSTOMREQUEST => 'POST', // Request method
        CURLOPT_POSTFIELDS => $data, // POST data
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic Og==' // Example authorization header
        ),
    ));
    
    // Execute cURL request
    $result = curl_exec($curl);
    // Check for errors
    if(curl_errno($curl)) {
        // Handle cURL error
        $error_message = curl_error($curl);
        // You may want to log or handle the error appropriately
        return "cURL Error: " . $error_message;
    }

    // Close cURL session
    curl_close($curl);

    // Decode the JSON response
    $result = json_decode($result, true);

    // Check the result of the API call
    if ($result && isset($result['status']) && $result['status'] == 200) {
        // Redirect with success message if the API call was successful
        return redirect(route('product.index'))->with('success', $result['msg']);
    } else {
        // Redirect back with error message if the API call failed
        return redirect()->back()->with('error', $result['msg']);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $token= Auth::user()->user_token;
        $data_arr = ['user_token'=>$token];
        $categorydata = json_encode($data_arr);
        $data = $this->getAPI('product/'.$id.'?user_token='.$token);
        $data=$data['result'];
        $data['cat_id']=explode('|',$data['cat_id']);

        $categoryresult = $this->postAPI('category-list',$categorydata);
        $category=$categoryresult['result'];
        // dd($category);
        return view('admin.product.product_create',compact('data','category')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $token= Auth::user()->user_token;
        $data = $request->except('_token','_method');
        $data['user_token'] = $token;
        $data['cat_id']=implode('|',$request->cat_id);
            
        if ($request->hasFile('product_image')) {
            $folder = str_replace(" ", "_", $token);
            $image = $request->file('product_image');
            $destinationPath = '/uploads/' . $folder."/";
            $filename = $image->getClientOriginalName();
            $image->move(public_path($destinationPath), $filename);
            $data['product_image'] = url('/').$destinationPath.$filename;
        }
        
        $data = json_encode($data);
        $data = $this->postAPI('product-update/'.$request->id,$data);
        return redirect(route('product.index'))->with('success', $data['msg']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        // Get user token
        $token = Auth::user()->user_token;
            
        // Prepare data for API call
        $data_arr = ['user_token'=>$token, 'id'=>$id];
        $data = json_encode($data_arr);
        
        // Make API call to delete category
        $response = $this->postAPI('productDelete/' . $id, $data);
     // Check if API call was successful
     if ($response && isset($response['status']) && $response['status']==200) {
         return redirect(route('product.index'))->with('success', $response['msg']);
     }
      else {
         // If deletion fails, handle the error appropriately
         return redirect()->back()->with('error', $response['msg']);
     }
    }


    // for Display Services Page
    // Filter Category Wise
    // Search Bar
    public function productpage(Request $request,$token, $slug){
        
        // if(empty($request->token))
        // {
        // $token= 'FOREVER-MEDSPA';
        // $data_arr = ['user_token'=>$token];
        // $data = json_encode($data_arr);

        // $data = $this->postAPI('product-list', $data);
  
        // return view('product.index',compact('data'));
        // }
        // else
        // {
        // $token= strtoupper($request->token);
        // $data_arr = ['user_token'=>$token];
        // $data = json_encode($data_arr);
        // $data = $this->postAPI('product-list', $data);
        $category_result=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->where('slug',$slug)->first();
        $data=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->where('cat_id',$category_result->id)->paginate(10);
        $category=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
        $popular_service=Product::where('popular_service',1)->where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
        //  For Auto Search Complete
        $search_category = ProductCategory::where('cat_is_deleted', 0)
        ->where('user_token', 'FOREVER-MEDSPA')
        ->pluck('cat_name')
        ->toArray();
        $search_product=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->pluck('product_name')->toArray();
        $finalarray = array_merge($search_category,$search_product);

        $search = json_encode($finalarray);


        return view('product.index',compact('data','category','search','popular_service'));
        }




        //  Data Filter Category Wise
        // Advance Search From Category and services 
        public function productCategory(Request $request, $id){
            // $data=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->where('cat_id','LIKE',$id)->paginate(20);
            $data = Product::where('cat_id', 'LIKE', '%|' . $id . '|%')
                   ->orWhere('cat_id', 'LIKE', $id . '|%')
                   ->orWhere('cat_id', 'LIKE', '%|' . $id)
                   ->orWhere('cat_id', $id)
                   ->paginate(20);
            $category=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
            $popular_service=Product::where('popular_service',1)->where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
             //  For Auto Search Complete
             $search_category = ProductCategory::where('cat_is_deleted', 0)
             ->where('user_token', 'FOREVER-MEDSPA')
             ->pluck('cat_name')
             ->toArray();
             $search_product=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->pluck('product_name')->toArray();
             $finalarray = array_merge($search_category,$search_product);
 
             $search = json_encode($finalarray);
            return view('product.index',compact('data','category','search','popular_service'));


        }




        //  All Types Search
        // We Are Search From Category and services 
        public function ServicesSearch(Request $request){
            if(empty($request->search))
            {
                return redirect(route('product-page'));
            }

            $data=['keywords'=>$request->search];
            Search_keyword::create($data);
            $search = '%' . $request->search . '%';
                        
            $search_result = ProductCategory::where('cat_is_deleted', 0)
                ->where('user_token', 'FOREVER-MEDSPA')
                ->where('cat_name', '=', $search) // Assuming $search is defined somewhere
                ->first();


            if (!is_null($search_result) && $search_result->count() > 0) {
                $data = Product::where('product_is_deleted', 0)
                    ->where('user_token', 'FOREVER-MEDSPA')
                    ->where('cat_id', $search_result->id) // Assuming $search_result is defined and has an 'id' attribute
                    ->paginate(50);
            }

            else
            {
                $data = Product::where('product_is_deleted', 0)
                ->where('user_token', 'FOREVER-MEDSPA')
                ->where('product_name', 'LIKE', $search)
                ->orWhere('search_keywords', 'LIKE', $search)
                ->paginate(50);
            }
            
            // For Category List get in frontend
                $category=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
                $popular_service=Product::where('popular_service',1)->where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
             // For Category List get in frontend End

            //  For Auto and Advance Search Complete
            $search_category = ProductCategory::where('cat_is_deleted', 0)
            ->where('user_token', 'FOREVER-MEDSPA')
            ->pluck('cat_name')
            ->toArray();
            $search_product=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->pluck('product_name')->toArray();
            $finalarray = array_merge($search_category,$search_product);

            $search = json_encode($finalarray);
            return view('product.index',compact('data','category','search','popular_service'));
           
        }



//  For Generate Keywords Report View
//  View Keywords Search
        public function KeywordsReports(Request $request){
            $keywordsData = DB::table('search_keywords')
            ->select('keywords', DB::raw('count(*) as keyword_count'))
            ->groupBy('keywords')
            ->paginate(10);
            return view('admin.product.keyword_report',compact('keywordsData'));
        }




//  For Generate Keywords Report View
//  View Keywords Search
//  Data Export into Excel
        public function ExportDate(Request $request)
        {
            $filename = "keywords.xls";
            $response = new StreamedResponse(function() {
                $handle = fopen('php://output', 'w');
    
                // Set the column headers
                fputcsv($handle, ['#', 'Keywords', 'Number of Search'], "\t");
    
                // Fetch data from the database
                $results = DB::table('search_keywords')
                    ->select('keywords', DB::raw('COUNT(*) as Number_of_Search'))
                    ->groupBy('keywords')
                    ->get();
    
                // Loop through data and write to file
                $index = 1;
                foreach ($results as $row) {
                    fputcsv($handle, [$index, $row->keywords, $row->Number_of_Search], "\t");
                    $index++;
                }
    
                fclose($handle);
            }, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    
            return $response;
        }
        
        public function PopularService(Request $request,$id){
            $data = Product::where('id',$id)->paginate(10);
            $category=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
            $popular_service=Product::where('popular_service',1)->where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
             //  For Auto Search Complete
             $search_category = ProductCategory::where('cat_is_deleted', 0)
             ->where('user_token', 'FOREVER-MEDSPA')
             ->pluck('cat_name')
             ->toArray();
             $search_product=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->pluck('product_name')->toArray();
             $finalarray = array_merge($search_category,$search_product);
 
             $search = json_encode($finalarray);
             return view('product.index',compact('data','category','search','popular_service'));
        }

        public function productdetails(Request $request){
        
            // if(empty($request->token))
            // {
            // $token= 'FOREVER-MEDSPA';
            // $data_arr = ['user_token'=>$token];
            // $data = json_encode($data_arr);
    
            // $data = $this->postAPI('product-list', $data);
      
            // return view('product.index',compact('data'));
            // }
            // else
            // {
            // $token= strtoupper($request->token);
            // $data_arr = ['user_token'=>$token];
            // $data = json_encode($data_arr);
            // $data = $this->postAPI('product-list', $data);
    
            $data=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->paginate(10);
            $category=ProductCategory::where('cat_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
            $popular_service=Product::where('popular_service',1)->where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->get();
            //  For Auto Search Complete
            $search_category = ProductCategory::where('cat_is_deleted', 0)
            ->where('user_token', 'FOREVER-MEDSPA')
            ->pluck('cat_name')
            ->toArray();
            $search_product=Product::where('product_is_deleted',0)->where('user_token','FOREVER-MEDSPA')->pluck('product_name')->toArray();
            $finalarray = array_merge($search_category,$search_product);
    
            $search = json_encode($finalarray);
    
    
            return view('product.product_details',compact('data','category','search','popular_service'));
            }


    }

