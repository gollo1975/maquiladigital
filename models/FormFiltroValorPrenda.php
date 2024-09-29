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
    public $planta;
    public $cerrar_pago;
    public $operario;
    public $fecha_inicio;
    public $fecha_corte;
    public $operacion;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'idtipo','estado_valor','autorizado','cerrar_pago','planta','operario','operacion'], 'integer'],
            [['fecha_inicio', 'fecha_corte'], 'safe'],
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
            'planta' => 'Planta/Bodega',
            'operario' => 'Nombre operario:',
            'operacion' => 'Nombre de operacion:',
            'fecha_inicio' =>'Fecha de inicio:',
            'fecha_corte' => 'Fecha de corte:',
            
           
        ];
    }
     
    
}
