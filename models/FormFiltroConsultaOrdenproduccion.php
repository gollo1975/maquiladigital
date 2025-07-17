<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroConsultaOrdenproduccion extends Model
{
    public $idcliente;
    public $desde;
    public $hasta;
    public $codigoproducto;
    public $facturado;
    public $tipo;
    public $ordenproduccionint;
    public $ordenproduccioncliente;
    public $mostrar_resultado;


    public function rules()
    {
        return [

            [['idcliente','facturado','tipo','mostrar_resultado'], 'integer'],
            [['desde','hasta'], 'safe'],
            ['codigoproducto', 'default'],
            ['ordenproduccioncliente', 'default'],
            ['ordenproduccionint', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idcliente' => 'Nombre del cliente:',
            'codigoproducto' => 'Referencia:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
            'facturado' => 'Facturado:',
            'tipo' => 'Tipo:',
            'ordenproduccionint' => 'Tipo producto:',
            'ordenproduccioncliente' => 'Op cliente:',
            'mostrar_resultado' => 'mostrar_resultado',
        ];
    }
}
