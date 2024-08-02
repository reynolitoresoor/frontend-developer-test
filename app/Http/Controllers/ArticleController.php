<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $articles = Article::with('writer','editor')->get()->toArray();
        return array_reverse($articles);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'link' => 'required',
            'date' => 'required',
            'content' => 'required',
            'company' => 'required'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/uploads');
            $imagePath = str_replace('public/', '', $imagePath);
            } else {
            $imagePath = null;
        }

        $article = Article::create([
            'image' => $imagePath,
            'title' => $validatedData['title'],
            'link' => $validatedData['link'],
            'date' => $validatedData['date'],
            'content' => $validatedData['content'],
            'status' => $request->status,
            'writer' => $request->writer,
            'editor' => $request->editor,
            'company' => $validatedData['company'],
            ]);
        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::find($id);
        return response()->json($article);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'company' => 'required',
            'title' => 'required',
            'content' => 'required',
            'link' => 'required',
            'date' => 'required'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/uploads');
            $imagePath = str_replace('public/', '', $imagePath);
            } else {
            $imagePath = null;
        }
        
        if($request->hasFile('image')) {
            $article = Article::where('id',$id)->update(
                ['image'=> $imagePath, 
                 'title' => $request->title, 
                 'content' => $request->content,
                 'link' => $request->link,
                 'date' => $request->date,
                 'status' => $request->status,
                 'company' => $request->company]
            );
        } else {
            $article = Article::where('id',$id)->update(
                ['title' => $request->title, 
                 'content' => $request->content,
                 'link' => $request->link,
                 'date' => $request->date,
                 'status' => $request->status,
                 'company' => $request->company]
            );
        }
        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::find($id);
        $article->delete();
        return response()->json('Article deleted!');
    }

    /**
     * Get user articles.
     */
    public function getUserArticles(Request $request, string $id)
    {
        $articles = Article::with('writer','editor')->where('writer',$id)->get()->toArray();
        return array_reverse($articles);
    }
}
