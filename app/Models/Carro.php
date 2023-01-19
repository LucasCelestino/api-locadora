<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = ['modelo_id','placa', 'disponivel','km'];

    public function rules()
    {
        return [
            'modelo_id'=>'exists:modelos,id',
            'placa'=>'required|min:7|max:7',
            'disponivel'=>'required|boolean',
            'km'=>'required'
        ];
    }

    public function feedback()
    {
        return [
            'modelo_id.exists'=>'Não existe um modelo com o id informado.',
            'placa.required'=>'O campo :attribute precisa ser preenchido.',
            'placa.min'=>'A placa precisa ter 7 digitos',
            'placa.max'=>'A placa precisa ter 7 digitos',
            'disponivel.required'=>'O campo :attribute precisa ser preenchido.',
            'km.required'=>'O campo :attribute precisa ser preenchido.',
        ];
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }
}
