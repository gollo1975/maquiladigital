<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pago_banco".
 *
 * @property int $id_pago_banco
 * @property int $nit_empresa
 * @property int $id_banco
 * @property int $tipo_pago
 * @property string $aplicacion
 * @property string $secuencia
 * @property string $fecha_creacion
 * @property string $fecha_aplicacion
 * @property string $descripcion
 * @property string $usuario
 *
 * @property Matriculaempresa $nitEmpresa
 * @property Banco $banco
 */
class PagoBanco extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pago_banco';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nit_empresa', 'id_banco', 'tipo_pago','autorizado','cerrar_proceso','total_empleados','total_pagar','id_tipo_nomina'], 'integer'],
            [['id_banco', 'tipo_pago', 'aplicacion', 'secuencia', 'fecha_creacion', 'fecha_aplicacion','id_tipo_nomina'], 'required'],
            [['fecha_creacion', 'fecha_aplicacion'], 'safe'],
            [['aplicacion', 'secuencia'], 'string', 'max' => 1],
            [['descripcion'], 'string', 'max' => 10],
            [['usuario','nit'], 'string', 'max' => 15],
            [['nit_empresa'], 'exist', 'skipOnError' => true, 'targetClass' => Matriculaempresa::className(), 'targetAttribute' => ['nit_empresa' => 'id']],
            [['id_banco'], 'exist', 'skipOnError' => true, 'targetClass' => Banco::className(), 'targetAttribute' => ['id_banco' => 'idbanco']],
            [['id_tipo_nomina'], 'exist', 'skipOnError' => true, 'targetClass' => TipoNomina::className(), 'targetAttribute' => ['id_tipo_nomina' => 'id_tipo_nomina']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_pago_banco' => 'Código',
            'nit_empresa' => 'Nit',
            'id_banco' => 'Banco',
            'tipo_pago' => 'Tipo proceso',
            'aplicacion' => 'Aplicación',
            'secuencia' => 'Secuencia',
            'fecha_creacion' => 'Fecha creacion',
            'fecha_aplicacion' => 'Fecha aplicacion',
            'descripcion' => 'Descripcion',
            'usuario' => 'Usuario',
            'total_empleados' => 'No empleados',
            'total_pagar' => 'Total pagar',
            'id_tipo_nomina' => 'Tipo pago',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNitEmpresa()
    {
        return $this->hasOne(Matriculaempresa::className(), ['id' => 'nit_empresa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanco()
    {
        return $this->hasOne(Banco::className(), ['idbanco' => 'id_banco']);
    }
    
    public function getTipoNomina()
    {
        return $this->hasOne(TipoNomina::className(), ['id_tipo_nomina' => 'id_tipo_nomina']);
    }
    
    // proceso de autorizado
    
    public function getEstadoAutorizado()
    {
        if($this->autorizado == 0){
            $estadoautorizado = 'NO';
        }else{
            $estadoautorizado = 'SI';
        }
        return $estadoautorizado;
    }
    
    // proces de cerrar proceso
    
     public function getEstadoCerrado()
    {
        if($this->cerrar_proceso == 0){
            $estadocerrado = 'NO';
        }else{
            $estadocerrado = 'SI';
        }
        return $estadocerrado;
    }
    // proceso tipo de pago
     public function getTipoPago()
    {
        if($this->tipo_pago == 220){
            $tipopago = 'PAGO PROVEEDORES';
        }else{
            $tipopago = 'PAGO NOMINA';
        }
        return $tipopago;
    }
    
     public function getAplicacionPago()
    {
        if($this->aplicacion == 'I'){
            $aplicacionpago = 'INMEDIATO';
        }else{
            if($this->aplicacion == 'M'){
              $aplicacionpago = 'MEDIO DIA';
            }else{
                  $aplicacionpago = 'NOCHE';
            }
        }
        return $aplicacionpago;
    }
    //PROCESO QUE BUSCA EL TIPO PROCESO
    
     public function getTipoProceso()
    {
        if($this->tipo_proceso == 1){
            $tipoproceso = 'PAGO VINCULADOS';
        }else{
            $tipoproceso = 'PAGO PRESTACION DE SERVICIOS';
        }
        return $tipoproceso;
    }
}
