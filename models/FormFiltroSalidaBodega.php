<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroSalidaBodega extends Model
{        
    public $referencia;
    public $codigo_producto;
    public $fecha_inicio;
    public $fecha_corte;

    public function rules()
    {
        return [            
            [['referencia'], 'integer'],
            [['fecha_inicio','fecha_corte'], 'safe'],
            [['codigo_producto'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [                        
            'referencia' => 'Referencia:',                      
            'codigo_producto' => 'Codigo:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
        ];
    }
    
}
