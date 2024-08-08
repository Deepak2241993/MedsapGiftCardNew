@if(isset($transaction_data))
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Laralink">
    <!-- Site Title -->
    <title>{{ $transaction_data->order_id }}|Invoice</title>
    <link rel="stylesheet" href="{{ url('/') }}/invoice_assets/assets/css/style.css">
</head>

<body>
    <div class="tm_container">
        <div class="tm_invoice_wrap">
            <div class="tm_invoice tm_style1" id="tm_download_section">
                <div class="tm_invoice_in">
                    <div class="tm_invoice_head tm_align_center tm_mb20">
                        <div class="tm_invoice_left">
                            <div class="tm_logo"><img
                                    src="{{url('/logo.png')}}"
                                    alt="Logo"></div>
                        </div>
                        <div class="tm_invoice_right tm_text_right">
                            <div class="tm_primary_color tm_f50 tm_text_uppercase">Invoice</div>
                        </div>
                    </div>
                    <div class="tm_invoice_info tm_mb20">
                        <div class="tm_invoice_seperator tm_gray_bg"></div>
                        <div class="tm_invoice_info_list">
                            <p class="tm_invoice_number tm_m0">Invoice No: <b
                                    class="tm_primary_color">#{{ $transaction_data->order_id }}</b></p>
                            <p class="tm_invoice_date tm_m0">Date: <b
                                    class="tm_primary_color">{{ date('m-d-Y', strtotime($transaction_data->updated_at)) }}</b>
                            </p>
                        </div>
                    </div>
                    <div class="tm_invoice_head tm_mb10">
                        <div class="tm_invoice_left">
                            <p class="tm_mb2"><b class="tm_primary_color">Invoice To:</b></p>
                            <p>
                                {{ $transaction_data->fname }} {{ $transaction_data->lname }}<br>
                                {{ $transaction_data->address }}<br>{{ $transaction_data->city }},{{ $transaction_data->country }}
                                - {{ $transaction_data->zip_code }}<br>
                                {{ $transaction_data->email }}
                            </p>
                        </div>
                        <div class="tm_invoice_right tm_text_right">
                            <p class="tm_mb2"><b class="tm_primary_color">Pay To:</b></p>
                            <p>
                                Forever MedSpa Wellness Center <br>
                                468 Paterson Ave East Rutherford <br>
                                NJ, 07073 <br>
                                <a href="mail:admin@forevermedspanj.com">admin@forevermedspanj.com</a>
              </p>
            </div>
          </div>
          <div class="tm_table tm_style1">
            <div class="tm_round_border tm_radius_0">
              <div class="tm_table_responsive">
                <table>
                  <thead>
                    <tr>
                      <th class="tm_width_3 tm_semi_bold tm_primary_color tm_gray_bg">Item</th>
                      <th class="tm_width_4 tm_semi_bold tm_primary_color tm_gray_bg">Description</th>
                      <th class="tm_width_1 tm_semi_bold tm_primary_color tm_gray_bg">No.Session</th>
                      <th class="tm_width_2 tm_semi_bold tm_primary_color tm_gray_bg tm_text_right">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                  $orderdata = \App\Models\ServiceOrder::where('order_id', $transaction_data->order_id)->get();
              @endphp
              @foreach ($orderdata as $key => $value)
                  @php
                      $ServiceData = \App\Models\Product::find($value->service_id);
                  @endphp
                  <tr class="tm_table_baseline">
                      <td class="tm_width_3 tm_primary_color">{{ $ServiceData->product_name }}</td>
                      <td class="tm_width_4">{{ Str::limit($ServiceData->short_description, 50, '...') }}</td>
                      <td class="tm_width_1">{{ $value->number_of_session }}</td>
                      <td class="tm_width_2 tm_text_right">${{ $ServiceData->discounted_amount }}</td>
                  </tr> @endforeach

                    
                                    </tbody>
                                    </table>
                        </div>
                    </div>
                    <div class="tm_invoice_footer
                                    tm_border_left tm_border_left_none_md">
                                    <div class="tm_left_footer tm_padd_left_15_md">
                                        <p class="tm_mb2"><b class="tm_primary_color">Payment info:</b></p>
                                        <p class="tm_m0">Payment Status -
                                            {{ ucFirst($transaction_data->payment_status) }}
                                            <br>Amount: ${{ $transaction_data->transaction_amount }}
                                        </p>
                                    </div>
                                    <div class="tm_right_footer">
                                        <table>
                                            <tbody>
                                                <tr class="tm_gray_bg tm_border_top tm_border_left tm_border_right">
                                                    <td class="tm_width_3 tm_primary_color tm_border_none tm_bold">
                                                        Subtoal</td>
                                                    <td
                                                        class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_bold">
                                                        ${{ $transaction_data->sub_amount }}</td>
                                                </tr>
                                                @if ($transaction_data->gift_card_amount != null)
                                                    @php
                                                        $giftamount = explode('|', $transaction_data->gift_card_amount);
                                                        $Totalgiftamount = 0; // Initialize the variable
                                                        foreach ($giftamount as $value) {
                                                            $Totalgiftamount += $value;
                                                        }
                                                    @endphp
                                                    <tr class="tm_gray_bg tm_border_left tm_border_right">
                                                        <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">
                                                            Giftcard Applied</td>
                                                        <td
                                                            class="tm_width_3 tm_text_right tm_border_none tm_pt0 tm_danger_color">
                                                            - ${{ $Totalgiftamount }}</td>
                                                    </tr>
                                                @endif

                                                <tr class="tm_gray_bg tm_border_left tm_border_right">
                                                    <td class="tm_width_3 tm_primary_color tm_border_none tm_pt0">Tax
                                                        <span class="tm_ternary_color">(10%)</span></td>
                                                    <td
                                                        class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_pt0">
                                                        +${{ $transaction_data->tax_amount }}</td>
                                                </tr>
                                                <tr class="tm_border_top tm_gray_bg tm_border_left tm_border_right">
                                                    <td
                                                        class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color">
                                                        Grand
                                                        Total </td>
                                                    <td
                                                        class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color tm_text_right">
                                                        ${{ $transaction_data->final_amount }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                        </div>
                    </div>
                    <hr class="tm_mb20">
                    <div class="tm_text_center">
                        <p class="tm_mb5"><b class="tm_primary_color">Terms & Conditions:</b></p>
                        <p class="tm_m0">Your use of the Website shall be deemed to constitute your understanding and
                            approval of, and agreement <br class="tm_hide_print">to be bound by, the Privacy Policy and
                            you
                            consent to the collection.</p>
                    </div><!-- .tm_note -->
                </div>
            </div>
            <div class="tm_invoice_btns tm_hide_print">
                <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
                    <span class="tm_btn_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                            <path
                                d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24"
                                fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32"
                                fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none"
                                stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <circle cx="392" cy="184" r="24" fill='currentColor' />
                        </svg>
                    </span>
                    <span class="tm_btn_text">Print</span>
                </a>
                <button id="tm_download_btn" class="tm_invoice_btn tm_color2">
                  <span class="tm_btn_icon">
                      <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                          <path
                              d="M320 336h76c55 0 100-21.21 100-75.6s-53-73.47-96-75.6C391.11 99.74 329 48 256 48c-69 0-113.44 45.79-128 91.2-60 5.7-112 35.88-112 98.4S70 336 136 336h56M192 400.1l64 63.9 64-63.9M256 224v224.03"
                              fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="32" />
                      </svg>
                  </span>
                  <span class="tm_btn_text">Download</span>
              </button>
            </div>
        </div>
    </div>
    <script src="{{ url('/') }}/invoice_assets/assets/js/jquery.min.js"></script>
    <script src="{{ url('/') }}/invoice_assets/assets/js/jspdf.min.js"></script>
    <script src="{{ url('/') }}/invoice_assets/assets/js/html2canvas.min.js"></script>
    <script src="{{ url('/') }}/invoice_assets/assets/js/main.js"></script>
</body>

</html>
@endif
