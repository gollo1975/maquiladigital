<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroEficienciaDiarioModular extends Model
{
    public $id_planta;
    public $fecha_actual;

    public function rules()
    {
        return [

            [['id_planta'], 'integer'],
            [['fecha_actual'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'id_planta' => 'Planta/Bodega:',
            'fecha_actual' => 'Fecha actual:',

        ];
    }
}
