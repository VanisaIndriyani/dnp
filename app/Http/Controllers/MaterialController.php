<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'admin') {
            $view = auth()->user()->role == 'super_admin' ? 'super_admin.materials.index' : 'admin.materials.index';

            // If category is selected, show materials for that category
            if ($request->has('category') && $request->category != '') {
                $category = $request->category;
                $materials = Material::where('category', $category)->latest()->paginate(10);
                return view($view, compact('materials', 'category'));
            }
            
            // Default: Dashboard for Super Admin and Admin
            $categories = ['cover', 'case', 'inner', 'endplate'];
            $stats = [];
            foreach ($categories as $cat) {
                $stats[$cat] = Material::where('category', $cat)->count();
            }
            return view($view, compact('stats'));
        }

        // Operator view - Filter by user's division
        $userDivision = auth()->user()->division;
        
        $materials = Material::query();
        
        if ($userDivision) {
            $materials->where('category', $userDivision);
        }
        
        $materials = $materials->latest()->paginate(10);
        return view('operator.materials.index', compact('materials'));
    }

    public function create(Request $request)
    {
        $category = $request->query('category');
        if (auth()->user()->role == 'super_admin') {
            return view('super_admin.materials.create', compact('category'));
        }
        return view('admin.materials.create', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category' => 'required',
            'file' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');

            Material::create([
                'title' => $request->title,
                'category' => $request->category,
                'file_path' => $path,
            ]);

            $redirectRoute = route(auth()->user()->role . '.materials.index');
            if ($request->has('category') && in_array($request->category, ['cover', 'case', 'inner', 'endplate'])) {
                $redirectRoute = route(auth()->user()->role . '.materials.index', ['category' => $request->category]);
            }

            return redirect($redirectRoute)->with('success', 'Materi berhasil diupload.');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    public function destroy(Material $material)
    {
        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();
        return redirect()->route(auth()->user()->role . '.materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}
