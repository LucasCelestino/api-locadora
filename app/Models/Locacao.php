<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = ['cliente_id','carro_id','data_inicio_periodo','data_final_previsto_periodo','data_final_realizado_periodo','valor_diaria','km_inicial','km_final'];

    public function rules()
    {
        return [
            'cliente_id'=>'exists:clientes,id',
            'carro_id'=>'exists:carros,id',
            'data_inicio_periodo'=>'required',
            'data_final_previsto_periodo'=>'required',
            'valor_diaria'=>'required|integer',
            'km_inicial'=>'required|integer',
        ];
    }

    public function feedback()
    {
        return [
            'cliente_id.exists'=>'Não existe um cliente com o id informado.',
            'carro_id.exists'=>'Não existe um carro com o id informado.',
            'data_inicio_periodo.required'=>'O campo :attribute precisa ser preenchido.',
            'data_final_previsto_periodo.required'=>'O campo :attribute precisa ser preenchido.',
            'valor_diaria.required'=>'O campo :attribute precisa ser preenchido.',
            'km_inicial.required'=>'O campo :attribute precisa ser preenchido.',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }
}
