<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formato_contrato_obralabor".
 *
 * @property int $id
 * @property int $id_contrato
 * @property int $id_empleado
 * @property int $id_formato_contenido
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property int $dias_trabajo
 * @property int $id_ingreso
 * @property string $fecha_hora_creacion
 * @property string $user_name
 *
 * @property Contrato $contrato
 * @property Empleado $empleado
 * @property FormatoContenido $formatoContenido
 * @property IngresoPersonalContrato $ingreso
 */
class FormatoContratoObralabor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formato_contrato_obralabor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_contrato', 'id_empleado', 'id_formato_contenido', 'dias_trabajo', 'id_ingreso','total_pagar'], 'integer'],
            [['fecha_inicio_periodo', 'fecha_corte_labor','fecha_corte_periodo', 'fecha_hora_creacion'], 'safe'],
            [['user_name'], 'string', 'max' => 15],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_formato_contenido'], 'exist', 'skipOnError' => true, 'targetClass' => FormatoContenido::className(), 'targetAttribute' => ['id_formato_contenido' => 'id_formato_contenido']],
            [['id_ingreso'], 'exist', 'skipOnError' => true, 'targetClass' => IngresoPersonalContrato::className(), 'targetAttribute' => ['id_ingreso' => 'id_ingreso']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_contrato' => 'Id Contrato',
            'id_empleado' => 'Id Empleado',
            'id_formato_contenido' => 'Id Formato Contenido',
            'fecha_inicio_periodo' => 'Fecha Inicio',
            'fecha_corte_labor' => 'Fecha Corte',
            'fecha_corte_periodo' => 'Fecha corte periodo',
            'dias_trabajo' => 'Dias Trabajo',
            'id_ingreso' => 'Id Ingreso',
            'fecha_hora_creacion' => 'Fecha Hora Creacion',
            'user_name' => 'User Name',
            'total_pagar' => 'total_pagar',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id_contrato' => 'id_contrato']);
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
    public function getFormatoContenido()
    {
        return $this->hasOne(FormatoContenido::className(), ['id_formato_contenido' => 'id_formato_contenido']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngreso()
    {
        return $this->hasOne(IngresoPersonalContrato::className(), ['id_ingreso' => 'id_ingreso']);
    }
}
