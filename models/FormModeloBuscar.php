<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormModeloBuscar extends Model
{
    public $q;   
    public $nombre;
    public $observacion;
    public $fecha_entrega;
    public $etapa;
    public $punto;
    public $clasificacion;



    public function rules()
    {
        return [

            [['q','nombre'], 'string'],  
            [['observacion'], 'string'],
            [['etapa','punto','clasificacion'], 'integer'],
            [['fecha_entrega'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'q' => 'Dato a Buscar:',  
            'nombre' =>'Presentacion:',
            'observacion' => 'Observacion:',
            'fecha_entrega' => 'F. entrega:',
            'etapa' => 'Etapa proceso:',
            'punto' => 'Punto de venta:',
            'clasificacion' => 'Clasificación:',

        ];
    }
}
