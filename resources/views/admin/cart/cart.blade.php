@extends('layouts.admin_layout')
@section('body')
    <!-- Body main wrapper start -->
    @php
        $cart = session()->get('cart', []);
        $amount = 0;
    @endphp
@push('css')
.cart-page-total {
    background-color: #f8f9fa; /* Light background to highlight the cart totals */
    border: 1px solid #ddd; /* Border around the totals section */
    padding: 20px;
    border-radius: 5px; /* Rounded corners */
}

.cart-page-total h2 {
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
    border-bottom: 1px solid #ddd; /* Line under heading */
    padding-bottom: 10px;
}

.cart-totals-list {
    list-style: none; /* Remove bullet points */
    padding: 0;
    margin: 0;
}

.cart-totals-item {
    display: flex; /* Flexbox to align items */
    justify-content: space-between; /* Space between label and value */
    padding: 10px 0; /* Spacing for each item */
    border-bottom: 1px solid #ddd; /* Line between items */
}

.cart-totals-item:last-child {
    border-bottom: none; /* Remove bottom line from last item */
}

.cart-totals-value {
    font-weight: bold; /* Bold values for emphasis */
    color: #333; /* Dark text color */
}

.fill-btn {
    display: block;
    width: 100%;
    text-align: center;
    margin-top: 20px;
    padding: 15px 0;
    background-color: #007bff; /* Primary button color */
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.fill-btn:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.fill-btn-inner {
    display: inline-block;
    position: relative;
}

.fill-btn-hover {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none; /* Hide hover text */
}

.fill-btn:hover .fill-btn-hover {
    display: inline-block; /* Show hover text */
}

.fill-btn:hover .fill-btn-normal {
    display: none; /* Hide normal text on hover */
}
@endpush
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-1">
                            <h3 class="mb-0">Cart</h3>
                           
                        </div>
                        <div class="col-md-2">
                            <div class="coupon2">
                                <button
                                    onclick="window.location.href='{{ route('product.index')}}'"
                                    class="btn btn-success" type="button">+Add More Service
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-10">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Cart View
                                </li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
        <!-- Breadcrumb area start  -->
        @if (isset($cart) && !empty($cart))
            <!-- Cart area start  -->
            <div class="cart-area section-space">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="product-thumbnail">Images</th>
                                            <th class="cart-product-name">Product</th>
                                            {{-- <th class="product-price">Unit Price</th> --}}
                                            <th class="product-quantity">No.of Session</th>
                                            <th class="product-subtotal">Total</th>
                                            <th class="product-remove">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $redeem = 0;
                                        @endphp
                            
                                        @foreach ($cart as $key => $item)
                                            @php
                                                $cart_data = App\Models\Product::find($item['product_id']);
                                                $amount += $cart_data->discounted_amount;
                                                $image = explode('|', $cart_data->product_image);
                                                if ($cart_data->giftcard_redemption == 1) {
                                                    $redeem += 1; // Corrected increment logic
                                                }
                                            @endphp
                            
                                            {{-- {{dd($cart_data)}} --}}
                                            <tr id="cart-item-{{ $cart_data->id }}">
                                                <td class="product-thumbnail"><a href="product-details.html"><img src="{{ $image[0] }}" alt="img"></a></td>
                                                <td class="product-name"><a href="product-details.html">{{ $cart_data->product_name }}</a></td>
                                                {{-- <td class="product-price"><span class="amount">$24.00</span></td> --}}
                                                <td class="product-quantity text-center">
                                                    <div class="product-quantity mt-10 mb-10">
                                                        <div class="product-quantity-form">
                                                            <input class="form-control" readonly type="text" value="{{ $cart_data->session_number }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="product-subtotal"><span class="amount">{{ $cart_data->discounted_amount }}</span></td>
                                                <td class="product-remove">
                                                    <a href="javascript:void(0)" onclick="removeFromCart({{ $item['product_id'] }})">
                                                        <i class="fa fa-trash" style="
                                                        font-size: 36px;
                                                    "></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="coupon-all">
                                        
                                        <div class="coupon d-flex align-items-center">
                                            <div class="row">

                                                @if ($redeem != 0)
                                                    <div class="col-9 mt-4">
                                                        <h5>Apply Giftcard</h5>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <input id="gift_number_0"
                                                                    placeholder="Enter Gift Card Number" class="form-control"
                                                                    name="coupon_code" type="text" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input id="giftcard_amount_0" placeholder="$0.00"
                                                                    class="form-control" name="coupon_code" type="number"
                                                                    min="0" onkeyup="validateGiftAmount(this)"
                                                                    readonly style="padding-left: 22px;">

                                                            </div>
                                                            <div class="col-md-3">
                                                                <button onclick="validategiftnumber({{ 0 }})"
                                                                    class="btn btn-success giftcartbutton" type="button">
                                                                    <i class="fa fa-check"
                                                                                aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <span class="text-danger mt-4" id="error_0"></span>
                                                                <span class="text-success mt-4" id="success_0"></span>
                                                            </div>
                                                            <div id="parentElement"></div>
                                                            <div class="col-md-5  mt-4 mb-4">
                                                                <button class="btn btn-primary" id="addGiftCardButton"
                                                                    type="button">Apply More
                                                                    Giftcard
                                                                    
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-6 ml-auto">
                                    <div class="cart-page-total">
                                        <h2>Cart totals</h2>
                                        <ul class="cart-totals-list mb-20">
                                            <li class="cart-totals-item">Subtotal <span class="cart-totals-value">${{ number_format($amount, 2) }}</span></li>
                                            <li class="cart-totals-item">Total Giftcard Applied <span class="cart-totals-value" id="giftcard_applied">$0</span></li>
                                            <li class="cart-totals-item">Tax 10% <span class="cart-totals-value" id="tax_amount">
                                                    @php
                                                        $taxamount = ($amount * 10) / 100;
                                                        echo "+$" . number_format($taxamount, 2);
                                                    @endphp
                                                </span></li>
                                            <li class="cart-totals-item">Total <span class="cart-totals-value" id="totalValue">${{ number_format($amount + $taxamount, 2) }}</span></li>
                                        </ul>
                                        <a class="fill-btn" href="javascript:void(0)" id="submitGiftCards">
                                            Proceed to checkout
                                           
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cart area end  -->
        @else
            <h3>Your Cart is Empty</h3>
        @endif

    </main>
    <!-- Body main wrapper end -->

    @endsection
    @push('script')
    <script>
        function removeFromCart(id) {
            $.ajax({
                url: '{{ route('cartremove') }}',
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: id
                },
                success: function(response) {
                    if (response.success) {
                        // Update the cart view, e.g., remove the item from the DOM
                        $('#cart-item-' + id).remove();
                        alert(response.success);
                        location.reload();
                    } else {
                        alert(response.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('An error occurred. Please try again.');
                }
            });
        }



        //  Gift card validation code start

        $(document).ready(function() {
            // Initialize key to a starting value
            var key = 0;
            // Array to store gift card numbers
            var giftCardNumbers = [];

            // Attach the click event to the button
            $('#addGiftCardButton').click(function() {
                // Increment the key for each new set of input fields
                key++;

                var html = `
            <div class="row mt-5" id="row_${key}">
                <div class="col-md-5">
                    <input id="gift_number_${key}" placeholder="Enter Gift Card Number"
                        class="form-control" name="coupon_code" type="text" required>
                </div>
                <div class="col-md-3">
                    <input id="giftcard_amount_${key}" placeholder="$0.00"
                        class="form-control" name="coupon_code" type="number" min="0" onkeyup="validateGiftAmount(this)" readonly style="padding-left: 22px;">
                </div>
                <div class="col-md-3" style="display:flex;">
                    <button onclick="validategiftnumber(${key})"
                        class="btn btn-success giftcartbutton" type="button">
                        <span class="fill-btn-inner">
                            <span class="fill-btn-normal"><i class="fa fa-check" aria-hidden="true"></i></span>
                            <span class="fill-btn-hover"><i class="fa fa-check" aria-hidden="true"></i></span>
                        </span>
                    </button> 
                    |
                    <button 
                        class="btn btn-danger giftcartdelete remove-button" type="button" data-key="${key}">
                        <span class="fill-btn-inner">
                            <span class="fill-btn-normal">X</span>
                            <span class="fill-btn-hover">X</span>
                        </span>
                    </button>
                </div>
                <div class="col-md-3">
                </div>
                <div class="col-md-12">
                    <span class="text-danger" id="error_${key}"></span>
                    <span class="text-success" id="success_${key}"></span>
                </div>
            </div>
        `;

                // Append the HTML to the desired parent element
                $('#parentElement').append(html); // Use the actual ID of the parent element
            });

            // Event delegation for dynamically added Remove buttons
            $(document).on('click', '.remove-button', function() {
                var keyToRemove = $(this).data('key');
                // Remove gift card number from the array
                var giftNumberToRemove = $('#gift_number_' + keyToRemove).val();
                giftCardNumbers = giftCardNumbers.filter(num => num !== giftNumberToRemove);
                $('#row_' + keyToRemove).remove();
                sumValues();
            });

            // Function to validate gift card number
            window.validategiftnumber = function(key) {
                var giftNumber = $('#gift_number_' + key).val();

                // Check if the gift card number is not null or empty
                if (!giftNumber) {
                    alert('Gift Card Number cannot be empty!');
                    $('#error_' + key).html('Gift Card Number cannot be empty.');
                    $('#success_' + key).html('');
                    return;
                }

                if (giftCardNumbers.includes(giftNumber)) {
                    alert('Duplicate Gift Card Number.');
                    $('#gift_number_' + key).val('');
                    $('#error_' + key).html('Duplicate Gift Card Number.');
                    $('#success_' + key).html('');
                    return;
                }

                $.ajax({
                    url: '{{ route('giftcards-validate') }}',
                    method: "post",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        giftcardnumber: giftNumber,
                        user_token: 'FOREVER-MEDSPA',
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            // Add the gift card number to the array
                            giftCardNumbers.push(giftNumber);

                            console.log(response.success);
                            console.log(response.result.total_amount);
                            $('#success_' + key).html(
                                'This Gift Card is valid. Your total available amount is $' +
                                response.result.total_amount);
                            $('#giftcard_amount_' + key).val(response.result.total_amount);
                            $('#giftcard_amount_' + key).removeAttr('readonly');
                            $('#giftcard_amount_' + key).attr('max', response.result.total_amount);
                            sumValues();
                            $('#error_' + key).html('');
                        } else {
                            alert('Invalid Giftcard. Please enter the correct number');
                            console.log(response.error);
                            $('#error_' + key).html(response.error || 'Invalid Giftcard. Please enter the correct number');
                            $('#success_' + key).html('');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('An error occurred. Please try again.');
                        $('#error_' + key).html('An error occurred. Please try again.');
                        $('#success_' + key).html('');
                    }
                });
            };
        });

        // Gift card validatuon code end

        // Adding Value in session
        $(document).ready(function() {
            $('#submitGiftCards').click(function() {
                var giftCards = [];

                // Add the initial gift card input fields
                var initialGiftNumber = $('#gift_number_0').val();
                var initialGiftAmount = $('#giftcard_amount_0').val();

                if (initialGiftNumber && initialGiftAmount) {
                    giftCards.push({
                        number: initialGiftNumber,
                        amount: initialGiftAmount
                    });
                }

                // Add dynamically added gift card input fields
                $('#parentElement .row').each(function() {
                    var rowId = $(this).attr('id').split('_')[1];
                    var giftNumber = $('#gift_number_' + rowId).val();
                    var giftAmount = $('#giftcard_amount_' + rowId).val();

                    if (giftNumber && giftAmount) {
                        giftCards.push({
                            number: giftNumber,
                            amount: giftAmount
                        });
                    }
                });

                $.ajax({
                    url: '{{ route('checkout') }}',
                    method: "post",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        giftcards: giftCards,
                        total_gift_applyed: $('#giftcard_applied').html().replace(/[\$-]/g, '')
                            .trim(),
                        tax_amount: $('#tax_amount').html().replace(/[\$+]/g, '').trim(),
                        totalValue: $('#totalValue').html().replace(/[\$]/g, '').trim()

                    },
                    success: function(response) {
                        if (response.status === 200) {
                            window.location = "{{ route('payment-process') }}";
                        } else {
                            alert('Error submitting Gift Cards: ' + response.error);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(
                            'An error occurred while submitting the Gift Cards. Please try again.');
                    }
                });
            });
        });


        // Giftcard number adding in session 

        let alertShownCount = 0;

        function validateGiftAmount(inputElement) {
            var maxValue = parseFloat($(inputElement).attr('max'));
            var currentValue = parseFloat($(inputElement).val());
            sumValues();
            if (currentValue > maxValue) {
                if (alertShownCount === 0) {
                    alert('The value entered exceeds the maximum allowed value of ' + maxValue +
                        '. Please enter a valid amount.');
                    alertShownCount++;
                    $(inputElement).val(maxValue);
                } else {
                    $(inputElement).val(maxValue);
                    // $(inputElement).prop('disabled', true);
                    alert('The value entered exceeds the maximum allowed value of ' + maxValue +
                        '. The value has been set to the maximum and the input field is now disabled.');
                }
            }
        }

        //  For Sum Calculation
        function sumValues() {
            let sum = 0;

            $('input[id^="giftcard_amount_"]').each(function() {
                let value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });

            var total_value_from_cart = {{ $amount }};
            var new_final_amount = (total_value_from_cart - sum);

            // Tax calculation 10%
            var taxamount = (new_final_amount * 10) / 100;

            $('#totalValue').text('$' + (new_final_amount + taxamount));
            $('#giftcard_applied').text('-$' + sum);
            $('#giftcard_applied').text('-$' + sum);
            $('#tax_amount').text('+$' + taxamount);
            //  $('#totalValue').text('Total Value: $' + sum.toFixed(2));
        }
    </script>
@endpush