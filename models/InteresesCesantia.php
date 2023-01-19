<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "intereses_cesantia".
 *
 * @property int $id_interes
 * @property int $id_programacion
 * @property int $id_grupo_pago
 * @property int $id_tipo_nomina
 * @property int $id_contrato
 * @property int $id_empleado
 * @property int $documento
 * @property string $inicio_contrato
 * @property string $fecha_inicio
 * @property string $fecha_corte
 * @property int $vlr_intereses
 * @property double $porcentaje
 * @property string $fecha_creacion
 * @property string $usuariosistema
 *
 * @property ProgramacionNomina $programacion
 * @property GrupoPago $grupoPago
 * @property TipoNomina $tipoNomina
 * @property Empleado $empleado
 * @property Contrato $contrato
 */
class InteresesCesantia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'intereses_cesantia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_programacion', 'id_grupo_pago', 'id_tipo_nomina', 'id_contrato', 'id_empleado', 'documento', 'vlr_intereses',
                'salario_promedio','vlr_cesantia','id_periodo_pago_nomina','dias_generados','importado','enviado'], 'integer'],
            [['inicio_contrato', 'fecha_inicio', 'fecha_corte', 'fecha_creacion'], 'safe'],
            [['porcentaje'], 'number'],
            [['usuariosistema'], 'string', 'max' => 30],
            [['id_programacion'], 'exist', 'skipOnError' => true, 'targetClass' => ProgramacionNomina::className(), 'targetAttribute' => ['id_programacion' => 'id_programacion']],
            [['id_grupo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoPago::className(), 'targetAttribute' => ['id_grupo_pago' => 'id_grupo_pago']],
            [['id_tipo_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNomina::className(), 'targetAttribute' => ['id_tipo_nomina' => 'id_tipo_nomina']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_periodo_pago_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => PeriodoPagoNomina::className(), 'targetAttribute' => ['id_periodo_pago_nomina' => 'id_periodo_pago_nomina']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_interes' => 'Id Interes',
            'id_programacion' => 'Id Programacion',
            'id_grupo_pago' => 'Id Grupo Pago',
            'id_tipo_nomina' => 'Id Tipo Nomina',
            'id_contrato' => 'Id Contrato',
            'id_empleado' => 'Id Empleado',
            'documento' => 'Documento',
            'inicio_contrato' => 'Inicio Contrato',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_corte' => 'Fecha Corte',
            'vlr_intereses' => 'Vlr Intereses',
            'porcentaje' => 'Porcentaje',
            'fecha_creacion' => 'Fecha Creacion',
            'usuariosistema' => 'Usuariosistema',
            'id_periodo_pago_nomina' =>'Periodo_pago_nomina',
            'dias_generados' =>'Dias_generados',
            'importado' => 'Pagado',
            'enviado' => 'enviado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramacion()
    {
        return $this->hasOne(ProgramacionNomina::className(), ['id_programacion' => 'id_programacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoNomina()
    {
        return $this->hasOne(TipoNomina::className(), ['id_tipo_nomina' => 'id_tipo_nomina']);
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
    public function getEnviadodato() {
        if($this->enviado == 0){
            $enviadodato = 'NO';
        }else{
            $enviadodato = 'SI';
        }
        return $enviadodato;
    }
}
