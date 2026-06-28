<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    // Get all inventory items
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->low_stock === 'true') {
            $query->whereRaw('quantity_in_stock <= reorder_level');
        }

        $items = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }

    // Get single item
    public function show($id)
    {
        $item = InventoryItem::findOrFail($id);

        return response()->json([
            'success' => true,
            'item'    => $item,
        ]);
    }

    // Create item
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|in:grocery,sewing_machine,disabled_bike,rickshaw,education,other',
            'unit'          => 'nullable|string',
            'unit_cost'     => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'supplier'      => 'nullable|string',
            'expiry_date'   => 'nullable|date',
            'description'   => 'nullable|string',
        ]);

        $sku  = 'SKU-' . strtoupper(Str::random(8));

        $item = InventoryItem::create([
            'name'          => $request->name,
            'sku'           => $sku,
            'category'      => $request->category,
            'description'   => $request->description,
            'unit'          => $request->unit ?? 'piece',
            'unit_cost'     => $request->unit_cost ?? 0,
            'reorder_level' => $request->reorder_level ?? 10,
            'supplier'      => $request->supplier,
            'expiry_date'   => $request->expiry_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully.',
            'item'    => $item,
        ], 201);
    }

    // Stock in (add stock)
    public function stockIn(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($id);
        $item->increment('quantity_in_stock', $request->quantity);

        return response()->json([
            'success'  => true,
            'message'  => $request->quantity . ' units added to stock.',
            'quantity' => $item->fresh()->quantity_in_stock,
        ]);
    }

    // Stock out (remove stock)
    public function stockOut(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($id);

        if ($item->quantity_in_stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $item->quantity_in_stock,
            ], 422);
        }

        $item->decrement('quantity_in_stock', $request->quantity);
        $item->increment('quantity_distributed', $request->quantity);

        return response()->json([
            'success'  => true,
            'message'  => $request->quantity . ' units removed from stock.',
            'quantity' => $item->fresh()->quantity_in_stock,
        ]);
    }

    // Get low stock alerts
    public function lowStock()
    {
        $items = InventoryItem::whereRaw('quantity_in_stock <= reorder_level')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
            'count'   => $items->count(),
        ]);
    }

    // Get expiring soon items
    public function expiringSoon()
    {
        $items = InventoryItem::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->whereDate('expiry_date', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
            'count'   => $items->count(),
        ]);
    }

    // Update item
    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully.',
            'item'    => $item,
        ]);
    }
}