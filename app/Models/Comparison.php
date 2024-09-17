<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    use HasFactory;

    protected $fillable = ['article1_id', 'article2_id', 'similarity'];

    public function article1()
    {
        return $this->belongsTo(NewsArticle::class, 'article1_id');
    }

    public function article2()
    {
        return $this->belongsTo(NewsArticle::class, 'article2_id');
    }
}
