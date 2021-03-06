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
    
    public function rules()
    {
        return [

            ['idcliente', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['desde', 'safe'],
            ['hasta', 'safe'],
            ['codigoproducto', 'default'],
            ['facturado', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['tipo', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['ordenproduccioncliente', 'default'],
            ['ordenproduccionint', 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idcliente' => 'Cliente:',
            'codigoproducto' => 'Referencia:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
            'facturado' => 'Facturado:',
            'tipo' => 'Tipo:',
            'ordenproduccionint' => 'Op interna:',
            'ordenproduccioncliente' => 'Op cliente:',
        ];
    }
}
