<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showPaymentPage(Order $order)
    {
        $this->authorize('pay', $order);
        
        return view('payments.checkout', compact('order'));
    }

    public function initiatePayment(Request $request, Order $order)
    {
        $this->authorize('pay', $order);
        
        // SSLCommerz API credentials
        $storeId = config('services.sslcommerz.store_id');
        $storePassword = config('services.sslcommerz.store_password');
        $isSandbox = config('services.sslcommerz.is_sandbox', true);
        
        $baseUrl = $isSandbox 
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
        
        // Customer info
        $customerInfo = $order->deliveryInfo;
        
        // Transaction info
        $transactionId = 'TRANS-' . uniqid();
        
        // Prepare the request data
        $postData = [
            'store_id' => $storeId,
            'store_passwd' => $storePassword,
            'total_amount' => $order->total,
            'currency' => 'BDT',
            'tran_id' => $transactionId,
            'success_url' => route('payments.success'),
            'fail_url' => route('payments.fail'),
            'cancel_url' => route('payments.cancel'),
            'ipn_url' => route('payments.ipn'),
            'cus_name' => $customerInfo->recipient_name,
            'cus_email' => auth()->user()->email,
            'cus_add1' => $customerInfo->address,
            'cus_city' => $customerInfo->city,
            'cus_state' => $customerInfo->state ?? '',
            'cus_postcode' => $customerInfo->zip_code,
            'cus_country' => 'Bangladesh',
            'cus_phone' => $customerInfo->phone,
            'shipping_method' => 'NO',
            'product_name' => 'Food Order #' . $order->order_number,
            'product_category' => 'Food',
            'product_profile' => 'general',
            'value_a' => $order->id,
        ];
        
        // Make API request to SSLCommerz
        $response = Http::post($baseUrl, $postData);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['status']) && $data['status'] === 'SUCCESS') {
                // Create payment transaction record
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                    'amount' => $order->total,
                    'payment_method' => 'SSLCommerz',
                    'currency' => 'BDT',
                    'status' => 'pending',
                    'payment_details' => json_encode($data),
                ]);
                
                // Redirect to SSLCommerz payment gateway
                return redirect()->away($data['GatewayPageURL']);
            }
        }
        
        // If there was an error
        return redirect()->route('orders.show', $order)
            ->with('error', 'Failed to initiate payment. Please try again.');
    }
    
    public function handleSuccess(Request $request)
    {
        $transactionId = $request->input('tran_id');
        $orderId = $request->input('value_a');
        
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'payment_details' => json_encode($request->all()),
            ]);
            
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['status' => 'confirmed']);
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment completed successfully!');
        }
        
        return redirect()->route('orders.index')
            ->with('error', 'Invalid transaction.');
    }
    
    public function handleFailure(Request $request)
    {
        $transactionId = $request->input('tran_id');
        $orderId = $request->input('value_a');
        
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'payment_details' => json_encode($request->all()),
            ]);
        }
        
        return redirect()->route('orders.show', $orderId)
            ->with('error', 'Payment failed. Please try again.');
    }
    
    public function handleCancel(Request $request)
    {
        $transactionId = $request->input('tran_id');
        $orderId = $request->input('value_a');
        
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'payment_details' => json_encode($request->all()),
            ]);
        }
        
        return redirect()->route('orders.show', $orderId)
            ->with('error', 'Payment cancelled.');
    }
    
    public function handleIPN(Request $request)
    {
        // Verify the payment with SSLCommerz
        $transactionId = $request->input('tran_id');
        $amount = $request->input('amount');
        $status = $request->input('status');
        
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        
        if ($transaction && $transaction->amount == $amount) {
            if ($status === 'VALID' || $status === 'VALIDATED') {
                $transaction->update([
                    'status' => 'completed',
                    'payment_details' => json_encode($request->all()),
                ]);
                
                $order = Order::find($transaction->order_id);
                if ($order) {
                    $order->update(['status' => 'confirmed']);
                }
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'payment_details' => json_encode($request->all()),
                ]);
            }
        }
        
        return response()->json(['status' => 'success']);
    }
}