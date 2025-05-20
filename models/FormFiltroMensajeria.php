<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroMensajeria extends Model
{
    public $proveedor;
    public $desde;
    public $hasta;


    public function rules()
    {
        return [

            [['proveedor'], 'integer'],
            [['desde','hasta'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'proveedor' => 'Nombre del proveedor:',
            'desde' => 'Fecha inicio:',
            'hasta' => 'Fecha corte:',
         ];
    }
}