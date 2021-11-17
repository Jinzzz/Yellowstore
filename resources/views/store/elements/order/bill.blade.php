


   <table width="100%" border="0" cellspacing="0" cellpadding="0">
  {{-- <tr>
    <td colspan="2"><img src="logo.png" width="150"  /></td>
  </tr> --}}
  {{-- <tr>
    <td colspan="2"> </td>
  </tr> --}}
  <tr>
    <td width="49%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;">Invoice From</td>
          </tr>
          @php
            $invoice_data = \DB::table('trn_order_invoices')->where('order_id',$order->order_id)->first();
         @endphp
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Invoice Number: {{@$invoice_data->invoice_id}}</td>
          </tr>
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Invoice Date: {{$changeDate = date("d-m-Y", strtotime( @$order->created_at))  }}</td>
          </tr>
          {{-- <tr>
            <td> </td>
          </tr> --}}
          {{-- <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;">Service Provider </td>
          </tr> --}}
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"> Yelowstore Home Infra solutions PVT Ltd<br>
                                    G1 - AVI Building 15/2 N.M Street<br>
                                    Krishnapuram,<br>
                                    Chennai - 600053<br>
                                    care@yellowstore.in
                                    <br>
            </td>
          </tr>
           <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"></td>
          </tr>
         {{-- <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">1001A, The Capital B Wing, 10th Floor, Bandra Kurla Complex, Bandra (E), Mumbai  </td>
          </tr>
          <tr>
            <td> </td>
          </tr> --}}
          {{-- <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">BBPS Biller Id: UGVCL0000GUJ01</td>
          </tr>
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">BBPS Transaction Id: PT01GYWT4625</td>
          </tr>
          <tr>
            <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;">Payment Channel: androidapp 8.14.55</td>
          </tr>
          <tr>
            <td> </td> --}}
          </tr>
          </table></td>
      </tr>
    </table></td>
    <td width="51%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      {{-- <tr>
        <td align="right"><img src="logo.png" alt="" width="150"  /></td>
      </tr> --}}
      {{-- <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right"></td>
      </tr> --}}
      {{-- <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"  align="right"> </td>
      </tr> --}}
      {{-- <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;"  align="right">Receipt Date : 01-12-2020</td>
      </tr> --}}
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:15px;" align="right">Invoice To</td>
      </tr>
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right">{{@$order->customer['customer_first_name']}} {{@$order->customer['customer_last_name']}}</td>
      </tr>
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right"> {{@$order->customer['customer_mobile_number']}}<br>
                                   {{@$order->customer['customer_address']}}<br>
                                    Pincode : {{$order->customer['customer_pincode']}}<br>
                                    {{@$order->customer['customer_email']}}
                                    </td>
      </tr>
      {{-- <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" align="right">demo@gmail.com</td>
      </tr> --}}
    </table></td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" width="34%" height="32" align="center">Item Name</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="26%" align="center">Qty</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="25%" align="center">Rate</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" width="15%" align="center">Tax</td>
      </tr>
   
   @foreach ($order_items as $order_item)
      <tr>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" height="32" align="center"> {{@$order_item->product->product_name}}</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">{{@$order_item->quantity}} </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">{{@$order_item->unit_price}} </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" align="center">{{@$order_item->total_amount}} </td>
      </tr>
   @endforeach

     @if(@$order->payment_type == '1')

     <tr colspan="2">
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="right"> Delivery Charge &nbsp;</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> {{ @$order->delivery_charge}}</td>
      </tr>

    @endif

     <tr colspan="2">
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="right"> Packing Charge &nbsp;</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> 20</td>
      </tr>

      <tr colspan="2">
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="right"> Discount &nbsp;</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> 0</td>
      </tr>

      <tr colspan="2">
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> </td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="right"> Total Amount &nbsp;</td>
        <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-bottom:1px  border-left:1px  border-right:1px " height="32" align="center"> {{ @$order->product_total_amount + 20 }}</td>
      </tr>
 
    </table></td>
  </tr>
  {{-- <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" colspan="2">Total Amount in Words: Three Thousand Seven Hundred Seventy Rupees Only</td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px;" colspan="2">Please Note:</td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" colspan="2">Dear Consumer, the bill payment will reflect in next 48 hours or in the next billing cycle, at your service provider’s end. Please  contact paytm customer support for any queries regarding this order.</td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px;" colspan="2">DECLARATION:</td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" colspan="2">This is not an invoice but only a confirmation of the receipt of the amount paid against for the service as described above.  Subject to terms and conditions mentioned at paytm.com</td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr> --}}
  {{-- <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px;" colspan="2" align="center">(This is computer generated receipt and does not require physical signature.)  <br />B-121 Sector 5, Noida, Uttar Pradesh 201301,<br />  Service tax registration number: AAACO4007ASD002<br />  Paytm Order ID :12252016430</td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr>
  <tr>
    <td colspan="2"> </td>
  </tr> --}}
</table>