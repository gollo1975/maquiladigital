<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "costos_gastos_empresa".
 *
 * @property int $id_costo_gasto
 * @property string $fecha_inicio
 * @property int $id
 * @property string $fecha_corte
 * @property int $total_costo_gasto
 * @property string $fecha_proceso
 * @property string $usuariosistema
 *
 * @property Matriculaempresa $id0
 */
class CostosGastosEmpresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costos_gastos_empresa';
    }
 public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->observacion = strtolower($this->observacion); 
        $this->observacion = ucfirst($this->observacion);  
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_inicio', 'fecha_corte'], 'required'],
            [['fecha_inicio', 'fecha_corte', 'fecha_proceso'], 'safe'],
            [['id', 'total_nomina','autorizado','total_seguridad_social','servicios','gastos_fijos','total_costos','total_ingresos','compras'], 'integer'],
            [['usuariosistema'], 'string', 'max' => 20],
            [['observacion'], 'string', 'max' => 100],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Matriculaempresa::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_costo_gasto' => 'Id Costo Gasto',
            'fecha_inicio' => 'Fecha Inicio',
            'id' => 'ID',
            'fecha_corte' => 'Fecha Corte',
            'total_costo_gasto' => 'Total nomina:',
            'total_seguridad_social' => 'Total seguridad:',
            'fecha_proceso' => 'Fecha Proceso',
            'usuariosistema' => 'Usuariosistema',
            'observacion' => 'Observación:',
            'autorizado' => 'Autorizado:',
            'servicios' => 'Servicios:',
            'gastos_fijos' => 'Gastos fijos:',
            'compras' => 'Compras:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaricula()
    {
        return $this->hasOne(Matriculaempresa::className(), ['id' => 'id']);
    }
    
    public function getAutorizadoCosto() {
        if($this->autorizado == 1){
            $autorizadocosto = 'SI';
        }else{
            $autorizadocosto = 'NO';
        }
        return $autorizadocosto;
    }
}
