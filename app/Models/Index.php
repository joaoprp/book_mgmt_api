<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Index extends Model
{
    protected $fillable = ['title', 'page', 'parent_id', 'book_id'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function recursiveCreate(array $data, Book $book, ?int $parentId = null)
    {
        $res = $book->indexes()->create([...$data, 'parent_id' => $parentId]);

        if (isset($data['indexes']) && count($data['indexes'])) {
            foreach ($data['indexes'] as $idx) {
                self::recursiveCreate($idx, $book, $res->id);
            }
        }
    }

    public static function recursiveUpdate(array $data, self $index)
    {
        $index->update($data);

        $res = $index->refresh();

        if (isset($data['indexes']) && count($data['indexes'])) {
            foreach ($data['indexes'] as $idx) {
                if (! isset($idx['id'])) {
                    self::recursiveCreate($idx, $res->book, $res->id);
                } else {
                    self::recursiveUpdate($idx, $res->children()->find($idx['id']));
                }
            }
        }
    }
}
