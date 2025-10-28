<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class WebsiteSettingController extends Controller
{
    public function index()
    {
        $data = [
            'college_name' => Setting::get('college_name'),
            'banner_heading' => Setting::get('banner_heading'),
            'banner_subheading' => Setting::get('banner_subheading'),
            'banner_button_text' => Setting::get('banner_button_text'),
            'banner_button_link' => Setting::get('banner_button_link'),
            'banner_image' => Setting::get('banner_image'),
            'top_banner_image' => Setting::get('top_banner_image'),
            'college_logo' => Setting::get('college_logo'),
            'favicon' => Setting::get('favicon'),
        ];

        return view('admin.settings.website', compact('data'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:255',
            'banner_heading' => 'nullable|string|max:255',
            'banner_subheading' => 'nullable|string|max:255',
            'banner_button_text' => 'nullable|string|max:100',
            'banner_button_link' => 'nullable|url',
            'top_banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'college_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:1024',
        ]);

        foreach ($validated as $key => $value) {
            if (!in_array($key, ['top_banner_image', 'banner_image', 'college_logo', 'favicon'])) {
                Setting::set($key, $value);
            }
        }

        if ($request->hasFile('top_banner_image')) {
            $path = $request->file('top_banner_image')->store('banners', 'public');
            Setting::set('top_banner_image', $path);
        }

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('banners', 'public');
            Setting::set('banner_image', $path);
        }

        if ($request->hasFile('college_logo')) {
            $path = $request->file('college_logo')->store('logos', 'public');
            Setting::set('college_logo', $path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicons', 'public');
            Setting::set('favicon', $path);
        }

        return back()->with('success', 'Website settings updated successfully!');
    }
}
