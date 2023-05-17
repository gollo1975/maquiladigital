<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ModelCrearComprobantes extends Model
{
    public $tipo_proceso;
    public $fecha_inicio;
    public $fecha_final;
    public $fecha_pago;
    public $tipo_comprobante;
    public $banco;

    public function rules()
    {
        return [            
            [['tipo_proceso','fecha_inicio','fecha_final','fecha_pago','tipo_comprobante','banco'],'required', 'message' => 'Campo requerido para generar el simulador'],
            [['tipo_proceso','tipo_comprobante'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['tipo_proceso','tipo_comprobante','banco'],'integer'],
            [['tipo_proceso'], 'default'],
            [['fecha_inicio','fecha_final','fecha_pago'], 'safe'],
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo_proceso' => 'Tipo proceso:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_final' => 'Fecha corte:',
            'fecha_pago' => 'Fecha de pago:',
            'tipo_comprobante'=> 'Tipo comprobante:',
            'banco' => 'Entidad bancaria:',
        ];
    }
}
