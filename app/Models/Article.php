<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'title',
        'link',
        'date',
        'content',
        'status',
        'writer',
        'editor',
        'company'
    ];

    /**
     * Get the writer.
     */
    public function writer()
    {
        return $this->belongsTo(User::class, 'writer', 'id');
    }

    /**
     * Get the editor.
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'editor', 'id');
    }

    /**
     * Get the editor.
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company', 'id');
    }

}
