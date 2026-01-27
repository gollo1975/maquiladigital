<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "matriculaempresa".
 *
 * @property string $nitmatricula
 * @property int $dv
 * @property string $razonsocialmatricula
 * @property string $nombrematricula
 * @property string $apellidomatricula
 * @property string $direccionmatricula
 * @property string $telefonomatricula
 * @property string $celularmatricula
 * @property string $emailmatricula
 * @property string $iddepartamento
 * @property string $idmunicipio
 * @property string $paginaweb
 * @property double $porcentajeiva
 * @property double $porcentajeretefuente
 * @property double $retefuente
 * @property double $porcentajereteiva
 * @property int $id_tipo_regimen
 * @property string $declaracion
 * @property int $id_banco_factura
 * @property int $idresolucion
 * @property string $nombresistema
 * @property int $gran_contribuyente
 * @property int $agente_retenedor
 * @property int $factura_venta_libre
 *
 * @property Banco $bancoFactura
 * @property TipoRegimen $tipoRegimen
 * @property Departamento $departamento
 * @property Municipio $municipio
 * @property Resolucion $resolucion
 */
class Matriculaempresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'matriculaempresa';
    }
     public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->representante_legal = strtoupper($this->representante_legal);        
        $this->emailmatricula = strtolower($this->emailmatricula);       
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nitmatricula', 'dv', 'razonsocialmatricula', 'nombrematricula', 'apellidomatricula', 'direccionmatricula', 'telefonomatricula', 'celularmatricula', 'emailmatricula', 'iddepartamento', 'idmunicipio', 'paginaweb', 'id_tipo_regimen', 'declaracion', 'gran_contribuyente','agente_retenedor', 'porcentaje_cesantias', 'porcentaje_intereses', 'porcentaje_prima', 'porcentaje_vacacion'], 'required'],
            [['dv', 'id_tipo_regimen', 'id_banco_factura', 'gran_contribuyente','agente_retenedor','vlr_minuto_vinculado','vlr_minuto_contrato','codigo_salario','aplica_auxilio','base_auxilio','codigo_salario_auxilio','codigo_salario_pago_produccion','ajuste_caja','codigo_concepto_compra',
                'aplica_regla','porcentaje_minima_eficiencia','dias_trabajados','horas_mensuales','tiempo_maximo_operacion','aplica_modulo_compra','horas_realmente_trabajadas','sam_castigo','total_eventos','sam_minimo','maneja_tablet_aplicacion','aplica_regla_castigo'], 'integer'],
            [['porcentajeiva', 'porcentajeretefuente', 'retefuente', 'porcentajereteiva', 'porcentaje_cesantias', 'porcentaje_intereses', 'porcentaje_prima', 'porcentaje_vacacion','porcentaje_empresa','valor_minuto_confeccion','valor_minuto_terminacion'], 'number'],
            [['declaracion','nombresistema', 'representante_legal'], 'string'],
            [['nitmatricula', 'telefonomatricula', 'celularmatricula', 'iddepartamento', 'idmunicipio'], 'string', 'max' => 15],
            [['razonsocialmatricula', 'nombrematricula', 'apellidomatricula', 'direccionmatricula', 'emailmatricula', 'paginaweb'], 'string', 'max' => 40],
            [['representante_legal'], 'string', 'max' => 50],
            [['nitmatricula'], 'unique'],
            [['id_banco_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Banco::className(), 'targetAttribute' => ['id_banco_factura' => 'idbanco']],
            [['id_tipo_regimen'], 'exist', 'skipOnError' => true, 'targetClass' => TipoRegimen::className(), 'targetAttribute' => ['id_tipo_regimen' => 'id_tipo_regimen']],
            [['iddepartamento'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['iddepartamento' => 'iddepartamento']],
            [['idmunicipio'], 'exist', 'skipOnError' => true, 'targetClass' => Municipio::className(), 'targetAttribute' => ['idmunicipio' => 'idmunicipio']],
            [['factura_venta_libre'], 'default'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nitmatricula' => 'Nit:',
            'dv' => 'Dv:',
            'razonsocialmatricula' => 'Razon Social:',
            'nombrematricula' => 'Nombres:',
            'apellidomatricula' => 'Apellidos:',
            'direccionmatricula' => 'Dirección:',
            'telefonomatricula' => 'Telefono:',
            'celularmatricula' => 'Celular:',
            'emailmatricula' => 'Email:',
            'iddepartamento' => 'Departamento:',
            'idmunicipio' => 'Municipio:',
            'paginaweb' => 'Pagina Web:',
            'porcentajeiva' => 'Porcentaje Iva:',
            'porcentajeretefuente' => 'Porcentaje Rete Fuente:',
            'retefuente' => 'Base Rete Fuente:',
            'porcentajereteiva' => 'Porcentaje Rete Iva:',
            'id_tipo_regimen' => 'Tipo Regimen:',
            'declaracion' => 'Declaración:',
            'id_banco_factura' => 'Banco Factura:',
            'nombresistema' => 'Nombre Sistema:',
            'agente_retenedor' => 'Agente Retenedor:',
            'gran_contribuyente' => 'Gran Contribuyente:',
            'porcentaje_cesantias' => '% cesantias:',
            'porcentaje_intereses' => '% intereses:',
            'porcentaje_prima' => '% prima:',
            'porcentaje_vacacion' => '% vacacion:',
            'representante_legal' => 'Representante legal:',
            'vlr_minuto_vinculado' => 'Vr. minuto vinculado:',
            'vlr_minuto_contrato' => 'Vr. minuto contrato:',
            'porcentaje_empresa' => 'Porcentaje_empresa:',
            'ajuste_caja' => 'Ajuste caja:',
            'codigo_concepto_compra' => 'codigo_concepto_compra',
            'valor_minuto_confeccion' => 'Valor minuto confeccion:',
            'valor_minuto_terminacion' => 'Valor minuto terminacion:',
            'aplica_regla' =>'aplica_regla',
            'porcentaje_minima_eficiencia' => 'Eficiencia minima:',
            'dias_trabajados' => 'Dias de trabajo:',
            'horas_mensuales' => 'Horas mensuales:',
            'tiempo_maximo_operacion' => 'Maximo porcentaje:',
            'horas_realmente_trabajadas' => 'Horas realmente trabajadas:',
            'sam_minimo' => 'sam_minimo',
            'maneja_tablet_aplicacion' => 'Aplica tablet:',
            'aplica_regla_castigo' => 'Aplica regla de castigo:',
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBancoFactura()
    {
        return $this->hasOne(Banco::className(), ['idbanco' => 'id_banco_factura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoRegimen()
    {
        return $this->hasOne(TipoRegimen::className(), ['id_tipo_regimen' => 'id_tipo_regimen']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['iddepartamento' => 'iddepartamento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipio()
    {
        return $this->hasOne(Municipio::className(), ['idmunicipio' => 'idmunicipio']);
    }

   
}
