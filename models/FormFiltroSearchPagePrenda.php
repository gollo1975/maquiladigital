<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroSearchPagePrenda extends Model
{
    public $documento;
     public $fecha_inicio;
    public $fecha_corte;
    public $id_operario;
    public $planta;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documento', 'id_operario','planta'], 'integer'],
          [['fecha_inicio','fecha_corte'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operario' => 'Operario:',
             'documento' => 'Documento:',
            'fecha_corte' =>  'Fecha corte:', 
            'fecha_inicio' => 'Fecha inico:',
            'planta' => 'Planta/Bodega:',
            
        ];
    }
     
    
}
