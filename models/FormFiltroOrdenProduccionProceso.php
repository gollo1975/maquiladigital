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
    public $orden;
    public $idtipo;
    public $codigoproducto;
    public $ver_registro;
    
    public function rules()
    {
        return [

            ['idcliente', 'default' ],
            ['ordenproduccion', 'default'],
            [['idtipo', 'orden'],'integer'],
            ['codigoproducto', 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'idcliente' => 'Cliente:',
            'ordenproduccion' => 'Orden interna:',
            'idtipo' => 'Tipo:',
            'codigoproducto' => 'Referencia:',
            'orden' => 'Orden del cliente:'
        ];
    }
}
