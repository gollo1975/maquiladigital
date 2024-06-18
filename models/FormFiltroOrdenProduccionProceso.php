<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroOrdenProduccionProceso extends Model
{
    public $idcliente;
    public $ordenproduccion;
    public $idtipo;
    public $codigoproducto;
    public $ver_registro;
    
    public function rules()
    {
        return [

            ['idcliente', 'default' ],
            ['ordenproduccion', 'default'],
            ['idtipo', 'default'],
            ['codigoproducto', 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'idcliente' => 'Cliente:',
            'ordenproduccion' => 'Op. Interna:',
            'idtipo' => 'Tipo:',
            'codigoproducto' => 'Referencia:',
        ];
    }
}
