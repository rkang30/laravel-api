<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $type = $this->getRequestType($request);
        $books = Book::all();

        return $this->responseWithType($books, $type);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $type = $this->getRequestType($request);
        $input = $request->all();
        $input['author'] = $user->author;

        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
            'cover_image' => 'required',
            'price' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $book = Book::create($input);

        return $this->responseWithType($book, $type);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $book = Book::find($id);
        $type = $this->getRequestType($request);

        if (is_null($book)) {
            return response('Book not found.', 404)
            ->header('Content-Type', 'text/plain');
        }

        return $this->responseWithType($book, $type);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $user = $request->user();
        if ($book->author != $user->author) {
            return response('You are only allowed to update your own books.', 422)
            ->header('Content-Type', 'text/plain'); 
        }

        $input = $request->all();
        $type = $this->getRequestType($request);

        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
            'cover_image' => 'required',
            'price' => 'required'
        ]);

        if($validator->fails()){
            return response('Validation Error.', 422)
            ->header('Content-Type', 'text/plain');     
        }

        $book->title = $input['title'];
        $book->description = $input['description'];
        $book->cover_image = $input['cover_image'];
        $book->price = $input['price'];
        $book->save();

        return $this->responseWithType($book, $type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();
        if ($book->author != $user->author) {
            return response('You are only allowed to remove your own books.', 422)
            ->header('Content-Type', 'text/plain'); 
        }

        $type = $this->getRequestType($request);
        $book->delete();
        
        return $this->responseWithType($book, $type);
    }
}
