<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
//ESTE PROCESO SIRVE PARA EL CUPO AL CLIENTE Y EL NUEVO PRECIO DE VENTA PARA INVENTARIO DIRECTO
class ModeloNuevoProceso extends Model
{
    public $proceso;
    public $cedula;
    public $motivo;


    public function rules()
    {
        return [

            [['proceso','cedula','motivo'], 'required'],
            [['proceso','cedula','motivo'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'proceso' => 'Tipo de proceso:',
            'cedula' => 'Documento del empleado:',
            'motivo' => 'Motivo disciplinario'

        ];
    }
}
