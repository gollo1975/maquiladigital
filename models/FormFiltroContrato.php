<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroContrato extends Model
{
    public $identificacion;
    public $activo;
    public $id_grupo_pago;
    public $id_empleado;
    public $tipo_contrato;
    public $desde;
    public $hasta;
    public $pension;
    public $eps;

    public function rules()
    {
        return [

            ['identificacion', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['activo', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            [['id_empleado', 'id_grupo_pago', 'tipo_contrato','eps','pension'], 'integer'],
            [['desde', 'hasta'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'identificacion' => 'Nro Identificacion',
            'activo' => 'Contrato Activo:',
            'id_grupo_pago' => 'Grupo pago:',
            'id_empleado' => 'Empleado:',
            'tipo_contrato' => 'Tipo de contrato:',
            'desde' => 'Fecha inicio:',
            'hasta' => 'Fecha corte:',
            'pension' => 'Fondo de pension:',
            'eps' => 'Entidad de salud',
        ];
    }
}
