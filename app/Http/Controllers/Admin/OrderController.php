<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    private array $statuses = [
        'pending' => 'pending',
        'paid' => 'paid',
        'shipped' => 'shipped',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
    ];

    public function index()
    {
        return view('admin.orders.index', [
            'orders' => Order::with('items')->latest()->paginate(15),
            'statuses' => $this->statuses,
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load('items'),
            'statuses' => $this->statuses,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statuses))],
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('status', __('ui.messages.order_status_updated'));
    }
}
