<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroBusquedaEntradaMateria extends Model
{        
   
    public $id_entrada;
    public $fecha_inicio;
    public $fecha_corte;
    public $proveedor;
    public function rules()
    {
        return [  
           [['id_entrada','proveedor'], 'integer'],
           [['fecha_inicio','fecha_corte'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'id_entrada' => 'Numero entrada:',
            'proveedor' => 'Proveedor:',
            'fecha_inicio' => 'Desde:',
            'fecha_corte' => 'Hasta:',
        ];
    }
    
}