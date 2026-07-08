<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carrega categorias e conta quantos produtos cada uma tem
        $categories = Categoria::withCount('produtos')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categorias,name']);
        Categoria::create($request->all());
        return redirect()->back()->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $category = Categoria::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255|unique:categorias,name,' . $id]);
        
        $category->update($request->all());
        return redirect()->back()->with('success', 'Categoria atualizada!');
    }

    public function destroy($id)
    {
        $category = Categoria::withCount('produtos')->findOrFail($id);

        if ($category->produtos_count > 0) {
            return redirect()->back()->withErrors(['error' => 'Não pode apagar uma categoria que contém produtos!']);
        }

        $category->delete();
        return redirect()->back()->with('success', 'Categoria removida.');
    }
}
