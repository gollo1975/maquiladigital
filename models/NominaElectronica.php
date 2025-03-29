<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nomina_electronica".
 *
 * @property int $id_nomina_electronica
 * @property int $id_periodo_pago
 * @property int $id_tipo_nomina
 * @property int $id_contrato
 * @property int $id_programacion
 * @property int $codigo_documento
 * @property int $id_empleado
 * @property int $id_periodo_electronico
 * @property int $id_grupo_pago
 * @property int $codigo_empleado
 * @property int $documento_empleado
 * @property string $primer_nombre
 * @property string $segundo_nombre
 * @property string $primer_apellido
 * @property string $segundo_apellido
 * @property string $email_empleado
 * @property double $salario_contrato
 * @property int $type_worker_id
 * @property int $sub_type_worker_id
 * @property int $codigo_municipio
 * @property string $direccion_empleado
 * @property int $codigo_forma_pago
 * @property string $nombre_banco
 * @property string $nombre_cuenta
 * @property string $numero_cuenta
 * @property string $cune
 * @property string $qrstr
 * @property string $fecha_inicio_nomina
 * @property string $fecha_final_nomina
 * @property string $fecha_inicio_contrato
 * @property string $fecha_terminacion_contrato
 * @property string $fecha_envio_nomina
 * @property string $fecha_recepcion_dian
 * @property double $total_devengado
 * @property double $total_deduccion
 * @property double $total_pagar
 * @property string $user_name
 * @property int $generado_detalle
 * @property int $exportado_nomina
 *
 * @property PeriodoPago $periodoPago
 * @property TipoNomina $tipoNomina
 * @property Contrato $contrato
 * @property ProgramacionNomina $programacion
 * @property Empleado $empleado
 * @property GrupoPago $grupoPago
 * @property PeriodoNominaElectronica $periodoElectronico
 */
class NominaElectronica extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nomina_electronica';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_periodo_pago', 'id_tipo_nomina', 'id_contrato', 'id_empleado', 'id_periodo_electronico', 'id_grupo_pago', 'documento_empleado', 'type_worker_id', 'sub_type_worker_id', 'codigo_municipio',
                'codigo_forma_pago', 'generado_detalle', 'exportado_nomina','numero_nomina_electronica', 'salario_contrato', 'total_devengado',
                'total_deduccion', 'total_pagar','dias_trabajados'], 'number'],
            [['fecha_inicio_nomina', 'fecha_final_nomina', 'fecha_inicio_contrato', 'fecha_terminacion_contrato', 'fecha_envio_nomina', 'fecha_recepcion_dian',
                'fecha_envio_begranda','fecha_hora_eliminacion'], 'safe'],
            [['primer_nombre','segundo_nombre', 'primer_apellido', 'segundo_apellido'], 'string', 'max' => 10],
            [['email_empleado', 'nota'], 'string', 'max' => 60],
            [['direccion_empleado','nombre_completo'], 'string', 'max' => 50],
            [['nombre_banco'], 'string', 'max' => 40],
            [['nombre_cuenta', 'numero_cuenta'], 'string', 'max' => 20],
            [['cune','nuevo_cune'], 'string', 'max' => 350],
            [['qrstr'], 'string', 'max' => 2000],
            [['documento_activo'], 'integer'],
            [['user_name','codigo_documento','consecutivo'], 'string', 'max' => 15],
            [['id_periodo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => PeriodoPago::className(), 'targetAttribute' => ['id_periodo_pago' => 'id_periodo_pago']],
            [['id_tipo_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNomina::className(), 'targetAttribute' => ['id_tipo_nomina' => 'id_tipo_nomina']],
            [['id_contrato'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['id_contrato' => 'id_contrato']],
            [['id_empleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::className(), 'targetAttribute' => ['id_empleado' => 'id_empleado']],
            [['id_grupo_pago'], 'exist', 'skipOnError' => true, 'targetClass' => GrupoPago::className(), 'targetAttribute' => ['id_grupo_pago' => 'id_grupo_pago']],
            [['id_periodo_electronico'], 'exist', 'skipOnError' => true, 'targetClass' => PeriodoNominaElectronica::className(), 'targetAttribute' => ['id_periodo_electronico' => 'id_periodo_electronico']],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_nomina_electronica' => 'Id Nomina Electronica',
            'id_periodo_pago' => 'Id Periodo Pago',
            'id_tipo_nomina' => 'Id Tipo Nomina',
            'id_contrato' => 'Id Contrato',
            'codigo_documento' => 'Codigo Documento',
            'id_empleado' => 'Id Empleado',
            'id_periodo_electronico' => 'Id Periodo Electronico',
            'id_grupo_pago' => 'Id Grupo Pago',
            'documento_empleado' => 'Documento Empleado',
            'primer_nombre' => 'Primer Nombre',
            'segundo_nombre' => 'Segundo Nombre',
            'primer_apellido' => 'Primer Apellido',
            'segundo_apellido' => 'Segundo Apellido',
            'email_empleado' => 'Email Empleado',
            'salario_contrato' => 'Salario Contrato',
            'type_worker_id' => 'Type Worker ID',
            'sub_type_worker_id' => 'Sub Type Worker ID',
            'codigo_municipio' => 'Codigo Municipio',
            'direccion_empleado' => 'Direccion Empleado',
            'codigo_forma_pago' => 'Codigo Forma Pago',
            'nombre_banco' => 'Nombre Banco',
            'nombre_cuenta' => 'Nombre Cuenta',
            'numero_cuenta' => 'Numero Cuenta',
            'cune' => 'Cune',
            'qrstr' => 'Qrstr',
            'fecha_inicio_nomina' => 'Fecha Inicio Nomina',
            'fecha_final_nomina' => 'Fecha Final Nomina',
            'fecha_inicio_contrato' => 'Fecha Inicio Contrato',
            'fecha_terminacion_contrato' => 'Fecha Terminacion Contrato',
            'fecha_envio_nomina' => 'Fecha Envio Nomina',
            'fecha_recepcion_dian' => 'Fecha Recepcion Dian',
            'total_devengado' => 'Total Devengado',
            'total_deduccion' => 'Total Deduccion',
            'total_pagar' => 'Total Pagar',
            'user_name' => 'User Name',
            'generado_detalle' => 'Generado Detalle',
            'exportado_nomina' => 'Exportado Nomina',
            'consecutivo' => 'consecutivo',
            'numero_nomina_electronica' => 'numero_nomina_electronica',
            'fecha_envio_begranda' => 'fecha_envio_begranda',
            'documento_activo' => 'documento_activo',
            'fecha_hora_eliminacion' => 'fecha_hora_eliminacion',
            'nota' => 'nota',
            'nuevo_cune' => 'nuevo_cune',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriodoPago()
    {
        return $this->hasOne(PeriodoPago::className(), ['id_periodo_pago' => 'id_periodo_pago']);
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
    public function getGrupoPago()
    {
        return $this->hasOne(GrupoPago::className(), ['id_grupo_pago' => 'id_grupo_pago']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriodoElectronico()
    {
        return $this->hasOne(PeriodoNominaElectronica::className(), ['id_periodo_electronico' => 'id_periodo_electronico']);
    }
    
    
    public function getExportadoNomina() {
        if($this->exportado_nomina == 0){
            $exportadonomina = 'NO';
        }else{
            $exportadonomina = 'SI';
        }
        return $exportadonomina;
    }
    
    public function getDocumentoActivo() {
        if($this->documento_activo == 0){
            $documentoactivo = 'SI';
        }else{
            $documentoactivo = 'NO';
        }
        return $documentoactivo;
    }    

}
