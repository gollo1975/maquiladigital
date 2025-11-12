<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModeloEditarReglaDescuento extends Model
{
    public $fecha_inicio;  
    public $fecha_final;
    public $tipo_descuento;
    public $nuevo_valor;
    public $estado;
    public function rules()
    {
        return [
           [['nuevo_valor'],'required',  'message' => 'Campo requerido'],
           [['tipo_descuento','nuevo_valor','estado'], 'integer'],
           [['fecha_inicio','fecha_final'], 'safe'], 
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_descuento' => 'Tipo descuento:', 
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_final' => 'Fecha final:',
            'nuevo_valor' => 'Nuevo valor:',
            'estado' => 'Activo:',
        ];
    }
}
