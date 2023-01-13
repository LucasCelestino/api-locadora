<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome','imagem'];

    public function rules()
    {
        return [
            'nome'=>'required|unique:marcas|min:3',
            'imagem'=>'required'
        ];
    }

    public function feedback()
    {
        return [
            'nome.required'=>'O campo :attribute precisa ser preenchido.',
            'nome.unique'=>'Já existe uma marca com esse nome.',
            'nome.min'=>'O campo :attribute precisa ter no minímo 3 caracteres',
            'imagem.required'=>'O campo :attribute precisa ser preenchido.'
        ];
    }
}
