<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
//este proceso sirve para subir el documento de produccion y subir las cantidades despachas
class ModeloDocumento extends Model
{
    public $documento;
    public $cantidad_vendida;
    public $cantidad_solicitadas;
    public $cantidad_despachada;
    public $numero_guia;
    public $posicion;
    public $codigo_caja;
    public $transportadora;

    public function rules()
    {
        return [

           [['documento','cantidad_vendida','cantidad_despachada','posicion','codigo_caja','transportadora','cantidad_solicitadas'], 'integer'],
            [['numero_guia'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'documento' => 'Documento produccion:',
            'cantidad_despachada' => 'Cantidad despachada:',
            'cantidad_vendida' => 'Cantidad vendida:',
            'posicion' => 'Posiciones:',
            'codigo_caja' => 'codigo_caja',
            'numero_guia' => 'Numero de guia:',
            'transportadora' => 'Transportadora:',
            'cantidad_solicitadas' => 'Cantidad solicitadas:'
           

        ];
    }
}
