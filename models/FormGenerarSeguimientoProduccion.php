<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormGenerarSeguimientoProduccion extends Model
{
    public $horastrabajar;
    public $operarias;
    public $minutos;
    public $reales;
    public $sistema;
    public $descanso;
    public $vlrprenda;

    public function rules()
    {
        return [            
            [['horastrabajar','operarias','minutos','descanso','reales'],'required', 'message' => 'Campo requerido para generar el informe'],
            ['operarias', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['horastrabajar', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['reales', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],            
            ['minutos', 'match', 'pattern' => '/^[.0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['descanso', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],            
            ['vlrprenda', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'operarias' => 'Nro Operarias',
            'horastrabajar' => 'Horas a Trabajar',
            'minutos' => 'Minutos Confeccion/Cliente:',
            'reales' => 'Prendas Reales:',
            'descanso' => 'Descanso (Minutos):',
            'vlrprenda' => 'Valor Prenda:',
        ];
    }
}
