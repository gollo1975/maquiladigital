<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EficienciaModuloDiario;

/**
 * EficienciaModuloDiarioSearch represents the model behind the search form of `app\models\EficienciaModuloDiario`.
 */
class EficienciaModuloDiarioSearch extends EficienciaModuloDiario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_eficiencia', 'id_planta'], 'integer'],
            [['fecha_actual', 'fecha_proceso', 'usuario_creador', 'usuario_editor'], 'safe'],
            [['total_eficiencia_planta'], 'number'],
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
        $query = EficienciaModuloDiario::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_eficiencia' => $this->id_eficiencia,
            'id_planta' => $this->id_planta,
            'fecha_actual' => $this->fecha_actual,
            'fecha_proceso' => $this->fecha_proceso,
            'total_eficiencia_planta' => $this->total_eficiencia_planta,
        ]);

        $query->andFilterWhere(['like', 'usuario_creador', $this->usuario_creador])
            ->andFilterWhere(['like', 'usuario_editor', $this->usuario_editor]);

        return $dataProvider;
    }
}
