<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormBuscarIntereses extends Model
{
    public $documento;
    public $id_grupo_pago;
    public $fecha_inicio;
    public $fecha_final;

    public function rules()
    {
        return [

            ['documento', 'default' ],
            ['id_grupo_pago', 'default'],
            [['fecha_final','fecha_inicio'],'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [

            'documento' => 'Documento:',
            'id_grupo_pago' => 'Grupo pago:',    
            'fecha_inicio' => 'Fecha inicio;',
            'fecha_final' => 'Fecha final:',
        ];
    }
}
