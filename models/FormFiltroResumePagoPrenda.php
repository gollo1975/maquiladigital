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
    public $exportado;
    public $validar_eficiencia;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'id_operario','operacion','registro_pagado','exportado','validar_eficiencia'], 'integer'],
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
             'operacion' => 'Proceso:',
            'fecha_corte' =>  'Fecha corte:', 
            'registro_pagado' => 'Registro pagado:',
            'exportado' => 'Exportado:',
            'validar_eficiencia' => 'Buscar eficiencia:',
            
        ];
    }
     
    
}
