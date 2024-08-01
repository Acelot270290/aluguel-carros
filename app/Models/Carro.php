<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo',
        'marca',
        'alugado',
        'img',
    ];

    public function alugueis()
    {
        return $this->hasMany(Aluguel::class);
    }
}
