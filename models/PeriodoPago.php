<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "periodo_pago".
 *
 * @property int $id_periodo_pago
 * @property string $nombre_periodo
 * @property int $dias
 * @property int $limite_horas
 * @property int $continua
 * @property double $periodo_mes
 */
class PeriodoPago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'periodo_pago';
    }

      public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre_periodo = strtoupper($this->nombre_periodo);        
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_periodo', 'dias', 'limite_horas', 'continua', 'periodo_mes'], 'required'],
            [['dias', 'limite_horas', 'continua'], 'integer'],
            [['periodo_mes'], 'number'],
            [['nombre_periodo','codigo_api_nomina'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_periodo_pago' => 'Id Periodo Pago',
            'nombre_periodo' => 'Nombre Periodo',
            'dias' => 'Dias',
            'limite_horas' => 'Limite Horas',
            'continua' => 'Continua',
            'periodo_mes' => 'Periodo Mes',
            'codigo_api_nomina' => 'Codigo api nomina',
        ];
    }
    
    public function PeriodoPago() 
    {
        return $this->hasMany(PeriodoPago::className(), ['id_periodo_pago' => 'id_periodo_pago']);
    }
    
}
