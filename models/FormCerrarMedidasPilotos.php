<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Contrato;

/**
 * ContactForm is the model behind the contact form.
 */
class FormCerrarMedidasPilotos extends Model
{        
    public $proceso_lavanderia;
    public $proceso_sin_lavanderia;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           [['proceso_sin_lavanderia','proceso_lavanderia'], 'integer'],
         
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [            
            'proceso_sin_lavanderia' => 'Orden sin lavanderia:',
            'proceso_lavanderia' => 'Orden con lavanderia:',
        ];
    }
    
   
    
}
