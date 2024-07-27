<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroPedido extends Model
{
    public $numero;
    public $referencia;
    public $cliente;
    public $fecha_inicio;
    public $fecha_corte;
    public $pedido;
    public $codigo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero', 'cliente','pedido','codigo'], 'integer'],
            [['fecha_inicio', 'fecha_corte'],'safe'],
            [['referencia'],'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'numero' => 'Numero orden:',
            'cliente' => 'Clientes::',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'pedido' => 'Numero pedido:',
            'codigo' => 'Codigo de referencia:',
            'referencia' => 'Referencia del producto:'
           
        ];
    }
     
    
}
