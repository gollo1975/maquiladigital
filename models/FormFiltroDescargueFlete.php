<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDescargueFlete extends Model
{
    public $proveedor;
    public $desde;
    public $hasta;
    public $numero;

    public function rules()
    {
        return [

            [['proveedor','numero'], 'integer'],
            [['desde','hasta'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'proveedor' => 'Proveedor:',
            'numero' => 'Numero de pago:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
       ];
    }
}
