<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroBanco extends Model
{
    public $id_banco;
    public $tipo_pago;
    public $fecha_inicio;
    public $fecha_corte;
    public $tipo_proceso;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_banco', 'tipo_pago','tipo_proceso'], 'integer'],
            [['fecha_inicio', 'fecha_corte'], 'safe'],
           // ['documento', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_banco' => 'Banco:',
            'tipo_pago' => 'Tipo pago:',
            'fecha_inicio' => 'Fecha corte:',
            'fecha_corte' => 'Fecha inicio:',
            'tipo_proceso' => 'Tipo proceso:',
           
        ];
    }
     
    
}
