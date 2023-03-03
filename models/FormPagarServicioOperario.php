<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormPagarServicioOperario extends Model
{        
   
    public $fecha_inicio;
    public $fecha_corte;
    public  $observacion;
    public $id_planta;

    public function rules()
    {
        return [            
           [['fecha_inicio','fecha_corte','id_planta'], 'required', 'message' => 'Campo requerido'],
            [['id_planta'], 'integer'],
            [['observacion'],'string' ,'max' => '50'],
            [['fecha_corte','fecha_inicio'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'observacion' => 'Observacion:',
            'id_planta' => 'Planta/Bodega:',
            
        ];
    }
    
}
