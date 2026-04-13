<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->has('title')) {
            return response()->json(Book::with('user:id,name')->whereRaw('word_similarity(title, ?) > 0.25', [$request->input('title')])->get());
        }

        return response()->json(Book::with('user:id,name')->get());
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pages' => ['required', 'integer', 'min:1'],
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'pages' => $validated['pages'],
            'user_id' => auth()->id(),
        ]);

        return response()->json($book, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $book = Book::findOrFail($id);

        // TODO: add reflection to validate only existing fields in payload before updating
        $book->update($request->all());

        return response()->json($book);
    }

    public function destroy(int $id): JsonResponse
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
