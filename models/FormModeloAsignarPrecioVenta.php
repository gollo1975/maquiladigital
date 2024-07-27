<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
//ESTE PROCESO SIRVE PARA EL CUPO AL CLIENTE Y EL NUEVO PRECIO DE VENTA PARA PEDISO
class FormModeloAsignarPrecioVenta extends Model
{
    public $nuevo_precio;
    public $nuevo_cupo;

    public function rules()
    {
        return [

           [['nuevo_precio','nuevo_cupo'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
             'nuevo_precio' => 'Nuevo precio de venta:',
            'nuevo_cupo' => 'Nuevo cupo comercial',
            

        ];
    }
}
