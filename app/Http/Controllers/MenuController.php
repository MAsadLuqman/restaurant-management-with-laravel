<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(20);

        $categories = Category::where('is_active', true)->get();

        return view('menu.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preparation_time' => 'required|integer|min:1|max:120',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item created successfully');
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load('category', 'inventoryItems');
        return view('menu.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preparation_time' => 'required|integer|min:1|max:120',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($data);

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item updated successfully');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->orderItems()->exists()) {
            return redirect()->route('menu-items.index')
                ->with('error', 'Cannot delete menu item with existing orders');
        }

        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return redirect()->route('menu-items.index')
            ->with('success', 'Menu item deleted successfully');
    }

    public function publicMenu(Request $request)
    {
        $table = null;
        if ($request->has('table')) {
            $table = \App\Models\Table::where('qr_code', $request->table)->first();
        }

        $categories = Category::with(['activeMenuItems' => function($query) {
            $query->orderBy('name');
        }])->where('is_active', true)->get();

        return view('menu.public', compact('categories', 'table'));
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $menuItem->is_available,
            'message' => 'Menu item availability updated'
        ]);
    }
}
