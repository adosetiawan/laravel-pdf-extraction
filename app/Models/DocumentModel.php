<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class DocumentModel extends Model
{
    use HasFactory, Searchable;

    protected $table = 'tb_documents';
    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'content',
        'extension',
        'mime_type',
    ];
    public function searchableAs(): string
    {
        return 'documents_index';
    }
    public function toSearchableArray()
{
    return [
        'id' => (int) $this->id,
        'content' => (float) $this->content,
    ];
}
}
