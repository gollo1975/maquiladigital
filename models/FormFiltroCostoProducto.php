<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroCostoProducto extends Model
{        
    public $codigo_producto;
    public $id_tipo_producto;
    public $fecha_creacion;
    public $descripcion;
    public $asignado;


    public function rules()
    {
        return [            
            [['codigo_producto','id_tipo_producto','asignado'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['descripcion'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [                        
            'codigo_producto' => 'Código producto:',                      
            'id_tipo_producto' => 'Tipo producto:',
            'fecha_creacion' => 'Fecha creación:',
            'descripcion' => 'Nombre:',
            'asignado' => 'Asignado:',
        ];
    }
    
}
