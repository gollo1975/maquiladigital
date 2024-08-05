<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDespachos extends Model
{
    public $proveedor;
    public $salida;
    public $referencia;
    public $fecha_inicio;
    public $fecha_corte;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['salida','proveedor'], 'integer'],
            [['referencia'], 'string'],
            [['fecha_inicio','fecha_corte'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'salida' => 'Salida de produccion:',
            'proveedor' => 'Nombre de proveedor:',
            'referencia' => 'Referencia despachada:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            
        ];
    }
     
    
}
