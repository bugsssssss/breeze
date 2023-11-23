<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ProtoneMedia\Splade\SpladeTable;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class PostController extends Controller
{
    public function index()
    {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('title', 'LIKE', "%{$value}%")
                        ->orWhere('slug', 'LIKE', "%{$value}%");
                });
            });
        });

        $posts = QueryBuilder::for(Post::class)
            ->defaultSort('title')
            ->allowedSorts(['title', 'slug'])
            ->allowedFilters(['title', 'slug', 'category_id',$globalSearch]);


        $categories = Category::pluck('name', 'id')->toArray();

        return view('posts.index', [
            'posts' => SpladeTable::for($posts)
                ->withGlobalSearch(columns: ['title'])
                ->defaultSort('id')
                ->column('id', canBeHidden: true,sortable: true)
                ->column('title', canBeHidden: false, sortable: true)
                ->column('slug',  sortable: true)
                ->selectFilter('category_id', $categories)
                ->column('created_at', sortable: true)
                ->column('action')
                ->paginate(25),
        ]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function show()
    {
        return view('posts.edit');
    }

    public function edit(PostStoreRequest $request)
    {
        $form = SpladeForm::make()
        ->action(route('edit', $request));
    }
}
