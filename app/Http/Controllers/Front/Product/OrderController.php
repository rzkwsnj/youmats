<?php


namespace App\Http\Controllers\Front\Product;


use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * @param Request $request
     * @param OrderItem $order
     * @return JsonResponse
     */
    public function get(Request $request, OrderItem $order): JsonResponse
    {
        return response()->json([
            'order' => $order->order,
            'item' => $order
        ]);
    }

    public function vendorUpdate(Request $request, Vendor $vendor)
    {
        if(!$vendor->active)
            return response(['status' => false, 'message' => __('Your account is not activated')]);

        $request->validate([
            'payment_status' => 'required|in:pending,refunded,completed',
            'status' => 'required|in:pending,shipping,refused,completed',
            'refused_note' => 'required_if:status,refused|max:191',
            'id' => 'required|numeric'
        ]);

        /**
         * TODO: Secure the update (Make sure $vendor really have that order)
         */

        $orderItem = OrderItem::findOrFail($request->id);

        $orderItem->payment_status = $request->payment_status;
        $orderItem->status = $request->status;

        if($request->status == 'refused')
            $orderItem->refused_note = $request->refused_note;

        //Finally, save the order item.
        $orderItem->save();

        //UPDATE ORDER STATUS & PAYMENT STATUS
        $orderStatus = OrderItem::select('status')
                            ->where('order_id', $orderItem->order_id)
                            ->groupBy('status')
                            ->limit(1)
                            ->first()
                            ->status;

        $paymentStatus = OrderItem::select('payment_status')
                            ->where('order_id', $orderItem->order_id)
                            ->groupBy('payment_status')
                            ->limit(1)
                            ->first()
                            ->payment_status;

        $order = Order::findOrFail($orderItem->order_id);
        $order->status = $orderStatus;
        $order->payment_status = $paymentStatus;
        $order->save();

        return back()->with(['custom_success' => __('Order Item has been updated')]);
    }
}
