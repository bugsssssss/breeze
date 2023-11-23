<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use ProtoneMedia\Splade\SpladeTable;
use App\Http\Requests\CategoryStoreRequest;

class CategoryController extends Controller
{
    public function index()
    {

        return view('categories.index', [
            'categories' => SpladeTable::for(Category::class)
                ->withGlobalSearch(columns: ['name', 'id'])
                ->defaultSort('id')
                ->column('id', canBeHidden: true,sortable: true)
                ->column('name', canBeHidden: false, sortable: true)
                ->column('slug')
                ->column('created_at', sortable: true)
                ->column('action')
                ->paginate(25),
        ]);
    }


    public function create()
    {
        return view('categories.create');
    }

    public function store(CategoryStoreRequest $request)
    {   
        Category::create($request->validated());

        return redirect()->route('categories.index');
    }
}
