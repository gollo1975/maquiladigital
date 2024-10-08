<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroModulos extends Model
{
    public $idcliente;
    public $idordenproduccion;
    public $fecha_inicio;
    public $activo;


    public function rules()
    {
        return [

            [['idcliente', 'idordenproduccion','activo'], 'integer'],
            ['fecha_inicio', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'idcliente' => 'Cliente:',
            'idordenproduccion' => 'Orden de Producción:',
            'fecha_inicio' => 'Fecha inicio:',
            'activo' => 'Modulo activo',
        ];
    }
}
