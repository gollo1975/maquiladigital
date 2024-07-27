<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormMaquinaBuscar extends Model
{
    public $q;    
    public $grupo;

    public function rules()
    {
        return [

            ['q', 'match', 'pattern' => '/^[a-z0-9\s]+$/i', 'message' => 'Sólo se aceptan números y letras'],   
            [['grupo'],'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'q' => 'Dato a Buscar:',   
            'grupo' => 'Grupo insumos:',
        ];
    }
}
