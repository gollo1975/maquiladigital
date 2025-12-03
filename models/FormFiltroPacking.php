<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroPacking extends Model
{
    public $numero;
    public $cliente;
    public $fecha_inicio;
    public $fecha_corte;
    public $transportadora;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero', 'cliente','transportadora'], 'integer'],
            [['fecha_inicio', 'fecha_corte'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'numero' => 'Numero packing:',
            'cliente' => 'Clientes::',
            'fecha_inicio' => 'Fecha inicio:',
            'fecha_corte' => 'Fecha corte:',
            'transportadora' =>'Nombre de la transportadora:'
           
        ];
    }
     
    
}
