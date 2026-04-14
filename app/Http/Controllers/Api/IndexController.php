<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Index;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Index::with('children')->whereNull('parent_id');
        if ($request->has('title')) {
            $query->whereRaw('word_similarity(title, ?) > 0.25', [$request->input('title')]);
        }

        return response()->json($query->get());
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'page' => ['required', 'integer'],
            'book_id' => ['required', 'integer'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        if (isset($validated['parent_id'])) {
            $parent = Index::findOrFail($validated['parent_id']);

            if ($validated['book_id'] !== $parent->book_id) {
                return response()->json(['error' => 'book_id does not match parent book_id'], Response::HTTP_BAD_REQUEST);
            }

            $index = $parent->children()->create($validated);

            return response()->json($index, Response::HTTP_CREATED);
        }

        $book = Book::findOrFail($validated['book_id']);

        $index = $book->indexes()->create($validated);

        return response()->json($index, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $index = Index::findOrFail($id);

        $index->update($request->all());

        if ($request->has('indexes')) {
            foreach ($request->input('indexes') as $idx) {
                if (! isset($idx['id'])) {
                    Index::recursiveCreate($idx, $index->book, $index->id);
                } else {
                    Index::recursiveUpdate($idx, $index->children()->find($idx['id']));
                }
            }
        }

        return response()->json($index);
    }

    public function destroy(int $id): Response
    {
        $index = Index::findOrFail($id);

        $index->delete();

        return response()->noContent();
    }
}
