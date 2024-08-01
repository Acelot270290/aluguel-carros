<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluguel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'carro_id',
        'data_inicio',
        'data_fim',
        'valor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
