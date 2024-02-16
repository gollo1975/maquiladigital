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
      /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idordenproduccion', 'id_operario','iddetalleorden'], 'integer'],
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
            'iddetalleorden' => 'Talla:',
                   
        ];
    }
     
    
}
