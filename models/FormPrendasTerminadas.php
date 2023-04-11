<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormPrendasTerminadas extends Model
{        
   
    public $id_entrada;
    public $id_balanceo;
    public  $idordenproduccion;
    public $iddetalleorden;
    public $cantidad_terminada;
    public $fecha_entrada;
    public $fecha_procesada;
    public $usuariosistema;
    public $observacion;
    public $nro_operarios;
    public $id_proceso_confeccion;
    public $hora_corte_entrada;


    public function rules()
    {
        return [            
           [['cantidad_terminada','fecha_entrada','hora_corte_entrada'], 'required', 'message' => 'Campo requerido'],
            [['cantidad_terminada','nro_operarios','id_proceso_confeccion'], 'integer'],
            [['observacion','hora_corte_entrada'], 'string', 'max' => 50],
            [['fecha_entrada'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [   
            'cantidad_terminada' => 'Nro prendas:',
            'fecha_entrada' => 'Fecha entrada:',
            'observacion' => 'Observación:',
            'nro_operarios'=> 'Nro perarios:',
            'id_proceso_confeccion' => 'Proceso confeccion:',
            'hora_corte_entrada' => 'Hora corte:',
            
        ];
    }
    
}
