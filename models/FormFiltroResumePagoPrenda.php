<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroResumePagoPrenda extends Model
{
    public $idordenproduccion;
    public $dia_pago;
    public $fecha_corte;
    public $id_operario;
    public $operacion;
    public $registro_pagado;
    public $id_planta;
    public $validar_eficiencia;
    public $tipo_servicio;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'id_operario','operacion','registro_pagado','id_planta','validar_eficiencia','tipo_servicio'], 'integer'],
          [['dia_pago','fecha_corte'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idordenproduccion' => 'Orden producción:',
            'dia_pago' => 'Fecha inicio:',
            'id_operario' => 'Operario:',
             'operacion' => 'Nombre de la operación:',
            'fecha_corte' =>  'Fecha corte:', 
            'registro_pagado' => 'Registro pagado:',
            'id_planta' => 'Planta/Bodega:',
            'validar_eficiencia' => 'Buscar eficiencia:',
            'tipo_servicio' => 'Tipo servicio:',
            
            
        ];
    }
     
    
}
