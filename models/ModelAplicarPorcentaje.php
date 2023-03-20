<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelAplicarPorcentaje extends Model
{
    public $planta;
    public $porcentaje;
    public $fecha_inicio;
    public $fecha_corte;
    public $tipo_empleado;



    public function rules()
    {
        return [            
            [['planta','fecha_inicio','fecha_corte','porcentaje'],'required', 'message' => 'Campo requerido para aplicar el porcentaje'],
            [['planta','tipo_empleado'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['fecha_inicio','fecha_corte'], 'safe'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'planta' => 'Planta/Bodega:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'porcentaje'=> '% Porcentaje:',
            'tipo_empleado' => 'Tipo empleado:',
        ];
    }
}
