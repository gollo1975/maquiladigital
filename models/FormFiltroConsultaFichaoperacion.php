<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroConsultaFichaoperacion extends Model
{
    public $idcliente;
    public $ordenproduccion;
    public $idtipo;
    public $codigoproducto;
    public $desde;
    public $hasta;
    
    public function rules()
    {
        return [

            ['idcliente', 'default' ],
            ['ordenproduccion', 'default'],
            ['idtipo', 'default'],
            ['codigoproducto', 'default'],
            ['desde', 'safe'],
            ['hasta', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'idcliente' => 'Cliente:',
            'ordenproduccion' => 'Op Interna:',
            'idtipo' => 'Servicio:',
            'codigoproducto' => 'Referencia:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
        ];
    }
}
