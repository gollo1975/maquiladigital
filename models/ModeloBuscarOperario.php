<?php

namespace app\models;

use Yii;
use yii\base\Model;
class ModeloBuscarOperario extends Model
{
    
    public $operario;
    public $fecha_entrada;
    public $aplica_sabado;
    public $modulo;
    public $id_detalle;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operario','aplica_sabado','modulo','id_detalle'], 'integer'],
            ['fecha_entrada', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
           'operario' => 'Nombre operario:',
           'fecha_entrada' => 'Fecha confección:',
           'aplica_sabado' => 'Aplica sabado:', 
            'modulo' => 'Nombre modulo:',
            'id_detalle' => 'Tallas:',
            
        ];
    }
     
    
}