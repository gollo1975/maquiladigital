<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroMaestroOperaciones extends Model
{
    public $idordenproduccion;
    public $id_operario;
    public $iddetalleorden;
    public $idproceso;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'id_operario','iddetalleorden','idproceso'], 'integer'],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idordenproduccion' => 'Orden producción:',
            'id_operario' => 'Operario:',
            'iddetalleorden' => 'Tallas:',
            'idproceso' => 'Operacion:',
                   
        ];
    }
     
    
}
