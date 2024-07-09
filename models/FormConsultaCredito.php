<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormConsultaCredito extends Model
{
    public $id_empleado;
    public $id_tipo_pago;
    public $codigo_credito;
    public $saldo;


    public function rules()
    {
        return [

            [['id_empleado', 'id_tipo_pago', 'codigo_credito'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['saldo','id_tipo_pago','codigo_credito'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_tipo_pago' => 'Tipo pago:',
            'id_empleado' => 'Empleado:',
            'codigo_credito' =>'Tipo crédito:',
            'saldo' => 'Saldo credito:',
        ];
    }
}