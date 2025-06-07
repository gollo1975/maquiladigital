<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FiltroProcesoDisciplinario extends Model
{        
   
    public $empleado;
    public $desde;
    public $hasta;
    public $grupo_pago;
    public $motivo;
    public $proceso;


    public function rules()
    {
        return [  
          
            [['empleado','proceso','grupo_pago','motivo'], 'integer'],
            [['desde','hasta'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'proceso' => 'Proceso disciplinario:',
            'empleado' => 'Nombre del empleado:',
            'grupo_pago' => 'Grupo de pago:',
            'desde' => 'Fecha inicio:',
            'hasta' => 'Fecha corte:',
            'motivo' => 'Motivo disciplinario:',

        ];
    }
    
}
