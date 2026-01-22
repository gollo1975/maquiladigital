<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroBitacora extends Model
{
    public $desde;
    public $hasta;
    public $operario;
    public $operacion;
    public $orden_produccion;
    public $inicio_hora_corte;
    public $final_hora_corte;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operario','operacion','orden_produccion'], 'integer'],
            [['inicio_hora_corte','final_hora_corte'], 'string'],
           [['desde', 'hasta'], 'required', 'message' => 'Este campo es obligatorio'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'operario' => 'Nombre del operario:',
            'desde' =>'Fecha de inicio:',
            'hasta' => 'Fecha de corte:',
            'operacion' => 'Nombre de la operación:',
            'orden_produccion' => 'Orden de produccion:',
            'inicio_hora_corte' => 'Inicio hora de corte:',
            'final_hora_corte' => 'Final hora de corte:',
        ];
    }
     
    
}
