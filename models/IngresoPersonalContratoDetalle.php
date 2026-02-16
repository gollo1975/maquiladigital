<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingreso_personal_contrato_detalle".
 *
 * @property int $id
 * @property int $id_ingreso
 * @property int $id_contrato
 * @property int $id_empleado
 * @property string $documento
 * @property string $nombre_completo
 * @property string $operacion
 * @property int $cantidad
 * @property int $valor_unitario
 * @property int $total_pagar
 * @property string $fecha_inicio
 *
 * @property IngresoPersonalContrato $ingreso
 * @property Empleado $empleado
 * @property Contrato $contrato
 */
class IngresoPersonalContratoDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ingreso_personal_contrato_detalle';
    }
    
    public function beforeSave($insert) {
	if(!parent::beforeSave($insert)){
            return false;
        }
	# ToDo: Cambiar a cliente cargada de configuración.    
	$this->operacion = strtoupper($this->operacion);
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_empleado','cantidad','valor_unitario','operacion'],'required', 'message' => 'Campo requerido'],
            [['id_ingreso', 'id_contrato', 'id_empleado', 'cantidad', 'valor_unitario', 'total_pagar','importado','total_dias'], 'integer'],
            [['fecha_inicio'], 'safe'],
            [['documento'], 'string', 'max' => 15],
            [['nombre_completo'], 'string', 'max' => 60],
            [['operacion'], 'string', 'max' => 50],
            [['id_ingreso'], 'exist', 'skipOnError' => true, 'targetClass' => IngresoPersonalContrato::className(), 'targetAttribute' => ['id_ingreso' => 'id_ingreso']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ingreso' => 'Id Ingreso',
            'id_contrato' => 'Id Contrato',
            'id_empleado' => 'Nombre del empleado:',
            'documento' => 'Documento',
            'nombre_completo' => 'Nombre Completo',
            'operacion' => 'Nombre de la operacion:',
            'cantidad' => 'Cantidad:',
            'valor_unitario' => 'Valor unitario:',
            'total_pagar' => 'Total Pagar',
            'fecha_inicio' => 'Fecha Inicio',
            'importado' => 'importado',
            'total_dias' => 'Dias del contrato:',
          
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngreso()
    {
        return $this->hasOne(IngresoPersonalContrato::className(), ['id_ingreso' => 'id_ingreso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::className(), ['id_empleado' => 'id_empleado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
    }
    
     public function getImportadoRegistro() {
        if ($this->importado == 0){
            $importado = 'NO';
        }else{
            $importado = 'Si';
        }
        return $importado;
        
    }
}
