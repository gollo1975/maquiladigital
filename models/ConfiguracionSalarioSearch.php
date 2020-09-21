<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ConfiguracionSalario;

/**
 * ConfiguracionSalarioSearch represents the model behind the search form of `app\models\ConfiguracionSalario`.
 */
class ConfiguracionSalarioSearch extends ConfiguracionSalario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_salario', 'salario_minimo_actual', 'salario_minimo_anterior', 'auxilio_transporte_actual', 'auxilio_transporte_anterior', 'salario_incapacidad', 'anio', 'estado'], 'integer'],
            [['porcentaje_incremento'], 'number'],
            [['usuario', 'fecha_creacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ConfiguracionSalario::find();

       $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id_salario' => SORT_DESC]] // Agregar esta linea para agregar el orden por defecto
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_salario' => $this->id_salario,
            'salario_minimo_actual' => $this->salario_minimo_actual,
            'salario_minimo_anterior' => $this->salario_minimo_anterior,
            'auxilio_transporte_actual' => $this->auxilio_transporte_actual,
            'auxilio_transporte_anterior' => $this->auxilio_transporte_anterior,
            'salario_incapacidad' => $this->salario_incapacidad,
            'porcentaje_incremento' => $this->porcentaje_incremento,
            'anio' => $this->anio,
            'estado' => $this->estado,
            'fecha_creacion' => $this->fecha_creacion,
        ]);

        $query->andFilterWhere(['like', 'usuario', $this->usuario]);
        $query->andFilterWhere(['like', 'salario_minimo_anterior', $this->salario_minimo_anterior]);

        return $dataProvider;
    }
}
