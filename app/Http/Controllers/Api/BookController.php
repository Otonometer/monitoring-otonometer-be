<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponse\ErrorResponse\BadRequestErrorResponse;
use App\Classes\ApiResponse\ErrorResponse\NotFoundErrorResponse;
use App\Classes\ApiResponse\SuccessResponse\CreatedResponse;
use App\Classes\ApiResponse\SuccessResponse\OKResponse;
use App\Http\Requests\Book\Update as BookUpdateRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\Store as BookStoreRequest;

class BookController extends Controller
{
    public function index(Request $request){
        $id = $request->query('id');
        if ($id) {
            $book = Book::find($id);
            if (!$book) {
                return (new NotFoundErrorResponse('Book not found'))->toResponse();
            }
            $book->increment('view_count');
            return (new OKResponse($book, 1))->toResponse();
        }

        $books = Book::query();

        $sort_by = $request->query('sortBy');

        if ($sort_by == 'rating' || $sort_by == 'title' || $sort_by == 'author' || $sort_by == 'created_at' || $sort_by == 'updated_at' || $sort_by == 'view_count' || $sort_by == 'download_count') {
            if ($request->query('sortOrder') == 'asc') {
                $books->orderBy($sort_by, 'asc');
            } else {
                $books->orderBy($sort_by, 'desc');
            }
        }

        $books = $books->get();


        return (new OKResponse($books, count($books)))->toResponse();
    }

    public function store(BookStoreRequest $request){
        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'rating' => $request->rating,
            'download_uri' => $request->download_uri,
        ];

        $file = $request->file('image');
        $fileext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $fileext;
        $path = 'images/book';

        $file->move($path, $filename);

        $data['image_uri'] = $path . '/' . $filename;


        $book = Book::create($data);
        return (new CreatedResponse($book))->toResponse();
    }

    public function update(BookUpdateRequest $request){
        $id = $request->query('id');

        if ($id == null) {
            return (new BadRequestErrorResponse('Book ID is required'))->toResponse();
        }

        $book = Book::find($request->id);
        if (!$book) {
            return (new NotFoundErrorResponse('Book not found'))->toResponse();
        }

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'rating' => $request->rating,
            'download_uri' => $request->download_uri,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $fileext;
            $path = 'images/book';

            $file->move($path, $filename);

            $data['image_uri'] = $path . '/' . $filename;

            if (file_exists($book->image_uri)) {
                unlink($book->image_uri);
            }
        }

        $book->update($data);
        return (new OKResponse($book, 1))->toResponse();
    }

    public function destroy(Request $request){
        $id = $request->query('id');
        if ($id == null) {
            return (new BadRequestErrorResponse('Book ID is required'))->toResponse();
        }
        $book = Book::find($request->id);

        if (!$book) {
            return (new NotFoundErrorResponse('Book not found'))->toResponse();
        }

        $book->delete();
        return (new OKResponse())->toResponse();
    }
}
