<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function rules()
    {
        return ['nome'=>'required|unique:marcas,nome,'.$this->id.'|min:3'];
    }

    public function feedback()
    {
        return ['nome.required'=>'O campo :attribute precisa ser preenchido.'];
    }
}
