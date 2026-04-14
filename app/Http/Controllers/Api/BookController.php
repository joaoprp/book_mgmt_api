<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Index;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Book::with('user:id,name')->with('indexes');
        if ($request->has('title')) {
            $query->whereRaw('word_similarity(title, ?) > 0.25', [$request->input('title')]);
        }

        return response()->json($query->get());
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pages' => ['required', 'integer', 'min:1'],
            'indexes' => ['sometimes', 'nullable', 'array'],
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'pages' => $validated['pages'],
            'user_id' => auth()->id(),
        ]);

        if (isset($validated['indexes'])) {
            foreach ($validated['indexes'] as $idx) {
                Index::recursiveCreate($idx, $book, null);
            }
        }

        return response()->json($book, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $book = Book::findOrFail($id);

        // TODO: add reflection to validate only existing fields in payload before updating
        $book->update($request->all());

        if ($request->has('indexes')) {
            foreach ($request->input('indexes') as $idx) {
                if (! isset($idx['id'])) {
                    Index::recursiveCreate($idx, $book, null);
                } else {
                    Index::recursiveUpdate($idx, $book->indexes()->find($idx['id']));
                }
            }
        }

        return response()->json($book);
    }

    public function destroy(int $id): JsonResponse
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
