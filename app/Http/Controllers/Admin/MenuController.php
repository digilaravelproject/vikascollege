<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->orderBy('order')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $menus = Menu::whereNull('parent_id')->get();
        return view('admin.menus.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        Menu::create($request->all());
        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully!');
    }

    public function edit(Menu $menu)
    {
        $menus = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        return view('admin.menus.edit', compact('menu', 'menus'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        $menu->update($request->all());
        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }
}
