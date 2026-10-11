<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsBanner;
use App\Models\CmsFaq;
use App\Models\CmsPage;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCmsController extends Controller
{
    public function banners()
    {
        $banners = CmsBanner::latest()->paginate(15);
        return view('admin.cms.banners', compact('banners'));
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'image_url' => 'required|string',
            'link_url'  => 'nullable|string',
            'position'  => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        CmsBanner::create($validated);
        return back()->with('success', 'Banner created.');
    }

    public function blogs()
    {
        $blogs = Blog::with(['category', 'tags'])->latest()->paginate(15);
        return view('admin.cms.blogs', compact('blogs'));
    }
}