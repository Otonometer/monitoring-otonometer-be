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
        ];

        $image = $request->file('image');
        $imageext = $image->getClientOriginalExtension();
        $imagename = time() . '.' . $imageext;
        $path = 'images/book';

        $image->move($path, $imagename);

        $data['image_uri'] = $path . '/' . $imagename;


        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $fileext;
            $path = 'storage/book';

            $file->move($path, $filename);

            $data['download_uri'] = $path . '/' . $filename;
        }

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
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageext = $image->getClientOriginalExtension();
            $imagename = time() . '.' . $imageext;
            $path = 'images/book';

            $image->move($path, $imagename);

            $data['image_uri'] = $path . '/' . $imagename;

            if (file_exists($book->image_uri)) {
                unlink($book->image_uri);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $fileext;
            $path = 'storage/book';

            $file->move($path, $filename);

            $data['download_uri'] = $path . '/' . $filename;

            if (file_exists($book->download_uri)) {
                unlink($book->download_uri);
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

        if (file_exists($book->download_uri)) {
            unlink($book->download_uri);
        }

        if (file_exists($book->image_uri)) {
            unlink($book->image_uri);
        }

        $book->delete();
        return (new OKResponse())->toResponse();
    }

    public function download(Request $request){
        $id = $request->query('id');

        if ($id == null) {
            return (new BadRequestErrorResponse('Book ID is required'))->toResponse();
        }

        $book = Book::find($request->id);

        if (!$book) {
            return (new NotFoundErrorResponse('Book not found'))->toResponse();
        }

        $book->increment('download_count');
        if (!file_exists($book->download_uri)) {
            return (new NotFoundErrorResponse('File not found'))->toResponse();
        }

        return response()->download($book->download_uri);
    }
}
