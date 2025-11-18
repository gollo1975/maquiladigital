<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroBusquedaAgentes extends Model
{        
   
    public $documento;
    public $nombre_completo;
    public $cargo;
    public $estado;


    public function rules()
    {
        return [  
          
           [['nombre_completo'], 'string'],
            [['documento','cargo','estado'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'documento' => 'Documento:',
            'nombre_completo' => 'Nombre agente comercial:',
            'cargo' => 'Nonmbre cargo:',
            'estado' => 'Activo:',

        ];
    }
    
}
