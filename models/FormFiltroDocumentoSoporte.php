<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroDocumentoSoporte extends Model
{
    public $proveedor;
    public $numero_compra;
    public $fecha_inicio;
    public $fecha_corte;
    public $numero_soporte;
    
    
    public function rules()
    {
        return [

            [['proveedor','numero_soporte'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['numero_compra'], 'string'],
            [['fecha_inicio','fecha_corte'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'proveedor' => 'Nombre proveedor:',
            'numero_compra' => 'Documento compra:',
            'fecha_inicio' => 'Fecha de inicio:',
            'fecha_corte' => 'Fecha de corte:',
            'numero_soporte' => 'Numero documento soporte:',
        ];
    }
}
