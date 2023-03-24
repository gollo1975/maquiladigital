<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Contrato;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCerrarModulo extends Model
{        
    public $hora_cierre;
    public $fecha_cierre;
    public $option;
    



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_cierre'],'required'],
            [['fecha_cierre'],'safe'],
            [['hora_cierre'],'string'],
            [['option'], 'integer'],
         
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [            
            'fecha_cierre' => 'Fecha cierre:',
            'hora_cierre' => 'Hora cierre:',
            'option' => 'Cerrar Orden de producción:',
        ];
    }
    
   
    
}
