<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroAsignacionProducto extends Model
{        
    public $proveedor;
    public $fecha_asignacion;
    public $orden_produccion;
    public $tipoOrden;
    public $documento;
    public $autorizado;
    public $fecha_corte;

    public function rules()
    {
        return [            
            [['proveedor','orden_produccion','tipoOrden','autorizado'], 'integer'],
            [['fecha_asignacion','fecha_corte'], 'safe'],
            [['documento'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [                        
            'proveedor' => 'Proveedor:',                      
            'fecha_asignacion' => 'Desde:',
            'orden_produccion' => 'Orden producción:',
            'tipoOrden' => 'Tipo proceso:',
            'autorizado' => 'Autorizado:',
            'documento' => 'Documento:',
            'fecha_corte' => 'Hasta:',
            
        ];
    }
    
}
