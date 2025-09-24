<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroConsultaCompra extends Model
{
    public $idproveedor;
    public $desde;
    public $hasta;
    public $numero;
    public $pendiente;
    public $planta;


    public function rules()
    {
        return [

            [['idproveedor','planta'], 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['desde', 'safe'],
            ['hasta', 'safe'],
            ['numero', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
            ['pendiente', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idproveedor' => 'Proveedor:',
            'numero' => 'N° Factura:',
            'desde' => 'Desde:',
            'hasta' => 'Hasta:',
            'pendiente' => 'Saldo Pendiente:',
            'planta' => 'Planta de producción:'
        ];
    }
}
