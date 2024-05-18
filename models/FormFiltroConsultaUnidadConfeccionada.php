<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroConsultaUnidadConfeccionada extends Model
{
    public $idordenproduccion;
    public $id_balanceo;
    public $fecha_inicio;
    public $fecha_corte;
    public $planta;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'id_balanceo','planta'], 'integer'],
          [['fecha_inicio','fecha_corte'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idordenproduccion' => 'Orden producción:',
            'fecha_inicio' => 'Fecha inicio:',
            'id_balanceo' => 'Nro balanceo:',
            'fecha_corte' =>  'Fecha corte:', 
            'planta' => 'Nombre de planta:',
           
        ];
    }
     
    
}
