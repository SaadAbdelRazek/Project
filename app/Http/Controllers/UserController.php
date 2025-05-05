<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => ['required', Password::min(6)],
            'phone'    => 'nullable|string',
            'address'  => 'nullable|string',
            'photo'    => 'nullable|image|max:2048',
            'role'     => 'nullable|in:user,vendor',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => 'nullable|string',
            'address'  => 'nullable|string',
            'photo'    => 'nullable|image|max:2048',
            'role'     => 'nullable|in:user,vendor',
            'is_active'=> 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('users/photos', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return new UserResource($user);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    //--------------------------------------------------------------
    public function getUsersWithOrderStats()
    {
        $users = User::where('role', 'user')
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                    'orders_count' => $user->orders_count,
                    'orders_total_price' => $user->orders_sum_total_price,
                ];
            });

        return response()->json($users);
    }

    //---------------------------------------------------------
    public function getUserOrderStats($id)
    {
        $user = User::with(['orders.products'])->findOrFail($id);

        $ordersData = $user->orders->map(function ($order) {
            return [
                'order_num'     => $order->order_num,
                'created_at'    => $order->created_at->toDateTimeString(),
                'order_status'  => $order->order_status,
                'products_count'=> $order->products->count(),
                'total_price'   => $order->total_price,
            ];
        });

        $ordersCount   = $user->orders->count();
        $totalAmount   = $user->orders->sum('total_price');
        $avgOrderPrice = $ordersCount > 0 ? round($totalAmount / $ordersCount, 2) : 0;
        $lastOrderDate = $user->orders->max('created_at');

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'photo'     => $user->photo,
                'created_at'=> $user->created_at,
                'role'      => $user->role,
            ],
            'orders' => $ordersData,
            'summary' => [
                'number_of_orders'      => $ordersCount,
                'total_orders_price'    => $totalAmount,
                'average_order_price'   => $avgOrderPrice,
                'last_order_date'       => $lastOrderDate ? $lastOrderDate->toDateTimeString() : null,
            ]
        ]);
    }

    //------------------------------------------------------------------

    //user orders-----------------------------------------------//-/------

    public function getUserOrders()
    {
        $user = Auth::user();

        $orders = Order::with(['products' => function ($query) {
            $query->select('products.id', 'products.name', 'products.price', 'products.image');
        }])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }


    //-------------------------------------------------
    //---------Specific order details------------------------

    public function getUserOrderByNumber($order_id)
    {
        $user = Auth::user();

        $order = Order::where('user_id', $user->id)
            ->where('id', $order_id)
            ->select('order_num', 'address', 'status', 'delivery_date')
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }


}
