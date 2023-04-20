<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eficiencia_modulo_diario".
 *
 * @property int $id_eficiencia
 * @property int $id_planta
 * @property string $fecha_actual
 * @property string $fecha_proceso
 * @property double $total_eficiencia_planta
 * @property string $usuario_creador
 * @property string $usuario_editor
 *
 * @property EficienciaModuloDetalle[] $eficienciaModuloDetalles
 * @property PlantaEmpresa $planta
 */
class EficienciaModuloDiario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eficiencia_modulo_diario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_planta', 'fecha_actual'], 'required'],
            [['id_planta','proceso_cerrado'], 'integer'],
            [['fecha_actual', 'fecha_proceso'], 'safe'],
            [['total_eficiencia_planta'], 'number'],
            [['usuario_creador', 'usuario_editor'], 'string', 'max' => 15],
            [['id_planta'], 'exist', 'skipOnError' => true, 'targetClass' => PlantaEmpresa::className(), 'targetAttribute' => ['id_planta' => 'id_planta']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_eficiencia' => 'Id',
            'id_planta' => 'Planta/Bodega:',
            'fecha_actual' => 'Nueva fecha:',
            'fecha_proceso' => 'Fecha proceso:',
            'total_eficiencia_planta' => 'Total Eficiencia Planta:',
            'usuario_creador' => 'Usuario creador:',
            'usuario_editor' => 'Usuario editor:',
            'proceso_cerrado' => 'Proceso cerrado:',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEficienciaModuloDetalles()
    {
        return $this->hasMany(EficienciaModuloDetalle::className(), ['id_eficiencia' => 'id_eficiencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanta()
    {
        return $this->hasOne(PlantaEmpresa::className(), ['id_planta' => 'id_planta']);
    }
    
    public function getProcesoCerrado(){
        if($this->proceso_cerrado == 0){
            $procesocerrado = 'NO';
        } else {
            $procesocerrado = 'SI';
        }
        return $procesocerrado;
    }
}
