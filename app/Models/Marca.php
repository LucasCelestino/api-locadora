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
            'nome'=>'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem'=>'required|file|mimes:png'
        ];
    }

    public function feedback()
    {
        return [
            'nome.required'=>'O campo :attribute precisa ser preenchido.',
            'nome.unique'=>'Já existe uma marca com esse nome.',
            'nome.min'=>'O campo :attribute precisa ter no minímo 3 caracteres',
            'imagem.required'=>'O campo :attribute precisa ser preenchido.',
            'imagem.mimes'=>'O arquivo deve ser uma imagem do tipo PNG.'
        ];
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}
