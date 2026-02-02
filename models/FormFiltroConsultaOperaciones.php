<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroConsultaOperaciones extends Model
{
    public $idproceso;
    public $idordenproduccion;
    public $id_tipo;
    public $totalRegistro;




    public function rules()
    {
        return [

            [['idproceso','totalRegistro','idordenproduccion','id_tipo'], 'integer' ],
         
        
        ];
    }

    public function attributeLabels()
    {
        return [

            'idproceso' => 'Operaciones:',
            'idordenproduccion' => 'Orden de Producción:',
            'id_tipo' => 'Maquina:',
            'totalRegistro' => 'Cantidad de registros:'
           
        ];
    }
}
