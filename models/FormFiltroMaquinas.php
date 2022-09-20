<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroMaquinas extends Model
{
    public $id_marca;
    public $id_tipo;
    public $fecha_desde;
    public $fecha_corte;
    public $codigo_maquina;
    public $modelo;
    public $bodega;
    public $estado;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_marca', 'id_tipo','bodega','estado'], 'integer'],
            [['codigo_maquina','modelo'], 'string'],
            [['fecha_desde', 'fecha_corte'], 'safe'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_marca' => 'Marca:',
            'id_tipo' => 'Tipo maquina:',
            'codigo_maquina' => 'Nro maquina:',
            'modelo' => 'Modelo:',
            'fecha_desde' => 'Fecha desde:',
            'fecha_corte' => 'Fecha corte:',
            'bodega' => 'Bodega:',
            'estado' => 'Maquina activa:',
            
        ];
    }
     
    
}
