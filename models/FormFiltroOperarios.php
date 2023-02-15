<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class FormFiltroOperarios extends Model
{
    public $id_operario;
    public $documento;
    public $estado;
    public $vinculado;
    public $planta;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operario', 'documento','estado','vinculado','planta'], 'integer'],
            ['documento', 'match', 'pattern' => '/^[0-9\s]+$/i', 'message' => 'Sólo se aceptan números'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_operario' => 'Operario:',
            'documento' => 'Documento:',
            'estado' => 'Activo:',
            'vinculado' => 'Vinculado:',
            'planta' => 'Planta / Bodega:',
            
        ];
    }
     
    
}
