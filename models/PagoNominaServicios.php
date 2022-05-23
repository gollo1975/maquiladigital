<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_nomina_servicios".
 *
 * @property int $id_pago
 * @property int $idordenproduccion
 * @property int $id_operario
 * @property int $documento
 * @property string $operario
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property string $fecha_registro
 * @property int $Total_pagar
 *
 * @property Ordenproduccion $ordenproduccion
 * @property Operarios $operario0
 */
class PagoNominaServicios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_nomina_servicios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_operario', 'documento', 'Total_pagar','total_dias','devengado','deduccion','autorizado'], 'integer'],
            [['id_operario', 'fecha_inicio', 'fecha_corte', 'Total_pagar'], 'required'],
            [['fecha_inicio', 'fecha_corte', 'fecha_registro'], 'safe'],
            [['operario','observacion','usuariosistema'], 'string', 'max' => 50],
            [['id_operario'], 'exist', 'skipOnError' => true, 'targetClass' => Operarios::className(), 'targetAttribute' => ['id_operario' => 'id_operario']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pago' => 'Id Pago',
            'id_operario' => 'Id Operario',
            'documento' => 'Documento',
            'operario' => 'Operario',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_corte' => 'Fecha Corte',
            'fecha_registro' => 'Fecha Registro',
            'Total_pagar' => 'Total Pagar',
            'observacion' => 'Observacion',
            'usuariosistema' => 'Usuario:',
            'total_dias' => 'Total dias;',
            'devengado' => 'devengado',
            'deduccion' => 'deduccion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperario()
    {
        return $this->hasOne(Operarios::className(), ['id_operario' => 'id_operario']);
    }
    
    public function getAutorizar (){
        if($this->autorizado == 0){
            $autorizado = 'NO';
        }else{
            $autorizado = 'SI';
        }
        return $autorizado;
    }
}
