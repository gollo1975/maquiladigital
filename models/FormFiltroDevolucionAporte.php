<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDevolucionAporte extends Model
{
    public $id_empleado;
    public $fecha_inicio;
    public $fecha_corte;
    public $concepto;

    public function rules()
    {
        return [

            [['id_empleado'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['concepto'], 'integer'],
            [['fecha_corte','fecha_inicio'], 'safe'],
           
        ];
    }

    public function attributeLabels()
    {
        return [
           
            'id_empleado' => 'Nombre del empleado:',
            'fecha_corte' => 'Fecha corte:',
            'fecha_inicio' => 'Fecha inicio:',
            'concepto' => 'Nombre del concepto:'
           
        ];
    }
}