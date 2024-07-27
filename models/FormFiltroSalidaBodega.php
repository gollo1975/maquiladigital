<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroSalidaBodega extends Model
{        
    public $orden_fabricacion;
    public $cliente;
    public $codigo_producto;
    public $fecha_inicio;
    public $fecha_corte;
    public $numero;

    public function rules()
    {
        return [            
            [['orden_fabricacion','cliente','numero'], 'integer'],
            [['fecha_inicio','fecha_corte'], 'safe'],
            [['codigo_producto'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [                        
            'orden_fabricacion' => 'Orden fabricacion:',                      
            'codigo_producto' => 'Codigo de referencia:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'cliente' => 'Nombre del cliente:',
            'numero' => 'Numero de salida:',
        ];
    }
    
}
