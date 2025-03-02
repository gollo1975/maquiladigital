<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroOrdenInsumo extends Model
{
    public $tipo_orden;
    public $desde;
    public $hasta;
    public $op_interna;
    public $op_cliente;
    public $numero_orden;
    public $referencia;


    public function rules()
    {
        return [

            [['tipo_orden', 'op_interna','numero_orden'], 'integer'],
            [['op_cliente','referencia'], 'string'],
            [['desde','hasta'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_orden' => 'Tipo orden:',
            'op_interna' => 'Op Interna:',
            'numero_orden' =>'Numero de orden:',
            'desde' =>'Fecha de inicio:',
            'hasta' =>'Fecha de corte:',
            'op_cliente' =>'Op Cliente:',
            'referencia' => 'Referencia:',
        ];
    }
}