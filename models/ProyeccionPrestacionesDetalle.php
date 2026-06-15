<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proyeccion_prestaciones_detalle".
 *
 * @property int $id_detalle
 * @property int $id_proyeccion
 * @property int $id_empleado
 * @property int $id_contrato
 * @property int $cedula_empleado
 * @property string $nombre_empleado
 * @property double $valor_prima
 * @property double $valor_cesantia
 * @property double $valor_intereses
 * @property double $valor_vacacion
 * @property int $numero_dias
 * @property double $total_linea
 *
 * @property ProyeccionPrestaciones $proyeccion
 * @property Empleado $empleado
 * @property Contrato $contrato
 */
class ProyeccionPrestacionesDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proyeccion_prestaciones_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_proyeccion', 'id_empleado', 'id_contrato', 'cedula_empleado', 'numero_dias'], 'integer'],
            [['valor_prima', 'valor_cesantia', 'valor_intereses', 'valor_vacacion', 'total_linea','salario_promedio'], 'number'],
            [['fecha_inicio','fecha_corte','fecha_inicio_contrato'], 'safe'],
            [['nombre_empleado'], 'string', 'max' => 60],
            [['id_proyeccion'], 'exist', 'skipOnError' => true, 'targetClass' => ProyeccionPrestaciones::className(), 'targetAttribute' => ['id_proyeccion' => 'id_proyeccion']],
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
            'id_detalle' => 'Id Detalle',
            'id_proyeccion' => 'Id Proyeccion',
            'id_empleado' => 'Id Empleado',
            'id_contrato' => 'Id Contrato',
            'cedula_empleado' => 'Documento',
            'nombre_empleado' => 'Nombre del empleado',
            'valor_prima' => 'Prima',
            'valor_cesantia' => 'Cesantia',
            'valor_intereses' => 'Intereses',
            'valor_vacacion' => 'Vacaciones',
            'numero_dias' => 'Numero Dias',
            'total_linea' => 'Total pagar',
            'fecha_inicio' => 'Fecha inicio',
            'fecha_corte' => 'Fecha corte',
            'fecha_inicio_contrato' => 'Fecha inicio contrato',
            'salario_promedio' => 'salario_promedio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProyeccion()
    {
        return $this->hasOne(ProyeccionPrestaciones::className(), ['id_proyeccion' => 'id_proyeccion']);
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
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->recalcularTotalLinea();
            return true;
        }
        return false;
    }
    
    public function recalcularTotalLinea()
    {
        // Usamos (float) para asegurar que se sumen números, incluso si están vacíos
        $this->total_linea = (
            (float)$this->valor_prima + 
            (float)$this->valor_cesantia + 
            (float)$this->valor_intereses + 
            (float)$this->valor_vacacion
        );
    }
    
    
}
