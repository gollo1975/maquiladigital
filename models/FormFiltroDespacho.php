<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDespacho extends Model
{
    public $cliente;
    public $pedido;
    public $fecha_inicio;
    public $fecha_corte;
    public $numero_despacho;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pedido', 'cliente','numero_despacho'], 'integer'],
            [['fecha_inicio', 'fecha_corte'],'safe'],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cliente' => 'Clientes:',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'pedido' => 'Numero pedido:',
            'numero_despacho' => 'Numero despacho:'
            
        ];
    }
     
    
}
