<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class questions extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'category_id',
        'title',
        'is_anonymous'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'question_id', 'id');
    }
}