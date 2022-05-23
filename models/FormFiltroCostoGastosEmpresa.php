<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroCostoGastosEmpresa extends Model
{
    public $fecha_inicio;
    public $fecha_corte;
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_corte', 'fecha_inicio'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fecha_corte' => 'Fecha corte:',
            'fecha_inicio' => 'Fecha inicio:',
        ];
    }
     
    
}
