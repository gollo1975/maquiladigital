<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormBuscarInsumos extends Model
{
    public $codigo;    
    public $nombre_producto;
    
    public function rules()
    {
        return [

            ['codigo', 'match', 'pattern' => '/^[a-z0-9\s]+$/i', 'message' => 'Sólo se aceptan números y letras'],   
            [['nombre_producto'],'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'codigo' => 'Codigo del producto:',   
            'nombre_producto' => 'Nombre del insumo:',
        ];
    }
}
