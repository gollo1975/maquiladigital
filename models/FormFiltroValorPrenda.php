<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroValorPrenda extends Model
{
    public $idordenproduccion;
    public $idtipo;
    public $estado_valor;
    public $autorizado;
    public $cerrar_pago;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'idtipo','estado_valor','autorizado','cerrar_pago'], 'integer'],
          // ['documento', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idordenproduccion' => 'Orden producción:',
            'idtipo' => 'Servicio:',
            'estado_valor' => 'Activo:',
            'autorizado' => 'Autorizado:',
            'cerrar_pago' => 'Cerrar pago:',
           
        ];
    }
     
    
}
