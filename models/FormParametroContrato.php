<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Contrato;

/**
 * ContactForm is the model behind the contact form.
 */
class FormParametroContrato extends Model
{        
    public $fecha_inicio;
    public $fecha_final;
    public $ibp_prima_inicial;    
    public $ibp_cesantia_inicial;
    public $ibp_recargo_nocturno;
    public $ultima_prima;
    public $ultima_cesantia;
    public $ultima_vacacion;
    public $ultimo_pago;
    public $id_contrato;
    public $id_grupo_pago;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ultima_prima','ultima_cesantia','ultima_vacacion','ultimo_pago'],'safe'],
            [['ibp_prima_inicial','ibp_cesantia_inicial','ibp_recargo_nocturno','id_grupo_pago'],'integer'],
            ['id_contrato','default'],
            ['fecha_final', 'fecha_error'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [            
            'fecha_inicio' => 'Fecha inicio contrato:',
            'fecha_final' => 'Fecha final contrato:',
            'ibp_prima_inicial' => 'Valor prima:',   
            'ibp_cesantia_inicial' => 'Valor cesantia:',
            'ibp_recargo_nocturno' => 'Recargo nocturno:',
            'ultima_prima' => 'Fecha ultima prima:',
            'ultima_cesantia' => 'Fecha ultima cesantia:',
            'ultima_vacacion' => 'Fecha ultima vacacion:',
            'ultimo_pago' => 'Fecha ultimo nomina:',
            'id_contrato' => 'Nro contrato:',
            'id_grupo_pago' =>  'Grupo de pago:'
        ];
    }
    
    public function fecha_error($attribute, $params)
    {
        //Buscar el email en la tabla
        $table = Contrato::find()->where([">","fecha_inicio",$this->fecha_final])->andWhere(["=","id_contrato",$this->id_contrato]);
        //Si es verdadero
        if ($table->count() == 1)
        {
            $this->addError($attribute, "La fecha inicio del contrato, no puede ser mayor a la fecha de retiro");
        }
    }
    
}
