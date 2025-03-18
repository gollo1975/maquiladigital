<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroResolucion extends Model
{
    public $numero;
    public $tipo_documento;
    public $estado;
   
    public function rules()
    {
        return [

            [['numero', 'tipo_documento', 'estado'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['numero','tipo_documento','estado'], 'integer'],
           
           
        ];
    }

    public function attributeLabels()
    {
        return [
            'numero' => 'Numero resolucion:',
            'tipo_documento' => 'Tipo documento:',
            'estado' =>'Activa:',
           
        ];
    }
}