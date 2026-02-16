<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroIngresoPersonalContrato extends Model
{
    public $fecha_inicio;
    public $fecha_corte;
    public $id_empleado;

    public function rules()
    {
        return [
            [['id_empleado'], 'integer'],
            [['fecha_inicio', 'fecha_corte'], 'safe'],
           
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'fecha_inicio' =>'Fecha de inicio:',
            'fecha_corte' => 'Fecha de corte:',
            'id_empleado' => 'Nombre del empleado:'
           
           
        ];
    }
}