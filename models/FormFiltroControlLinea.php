<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroControlLinea extends Model
{
    public $desde;
    public $hasta;
    public $operario;
    public $nueva_fecha;
    public $nueva_linea;
    public $validar_eficiencia;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operario','nueva_linea','validar_eficiencia'], 'integer'],
            [['desde', 'hasta','nueva_fecha'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'operario' => 'Nombre del operario:',
            'desde' =>'Fecha de inicio:',
            'hasta' => 'Fecha de corte:',
            'nueva_linea' => 'Ccambio de linea',
            'nueva_fecha' => 'Nueva_fecha',
            'validar_eficiencia' => 'Aplica ultima linea'
        ];
    }
     
    
}
