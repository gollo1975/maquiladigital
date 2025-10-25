<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroBuscarNomina extends Model
{
    public $desde;
    public $hasta;
    public $grupo_pago;
    public $empleado;


    public function rules()
    {
        return [
            [['grupo_pago','empleado'],'integer'],  
            [['desde', 'hasta'], 'safe'],
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'desde' => 'Fecha_inicio',
            'hasta' => 'Fecha corte:',
            'grupo_pago' => 'Grupo de pàgo:',
            'empleado' => 'Empleado:',
           
        ];
    }
}