<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelEficienciaModular extends Model
{
    public $orden_produccion;
    public $cliente;
    public $fecha_inicio;
    public $nro_balanceo;
    public $planta;
    public $fecha_corte;

    public function rules()
    {
        return [            
            [['cliente','orden_produccion','planta','nro_balanceo'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['cliente','orden_produccion','planta','nro_balanceo'],'integer'],
            [['cliente'], 'default'],
            [['fecha_inicio','fecha_corte'], 'safe'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'cliente' => 'Cliente:',
            'orden_produccion' => 'Op Interna:',
            'planta' => 'Planta / Sucursal:',
            'nro_balanceo' => 'Nro balanceo:',
            'fecha_inicio'=> 'Fecha inicio:',
            'fecha_corte'=> 'Fecha corte:',
        ];
    }
}
