<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NovedadOperario;

/**
 * NovedadOperarioSearch represents the model behind the search form of `app\models\NovedadOperario`.
 */
class NovedadOperarioSearch extends NovedadOperario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_novedad', 'id_tipo_novedad', 'id_operario', 'documento'], 'integer'],
            [['fecha_inicio_permiso', 'fecha_final_permiso', 'hora_inicio_permiso', 'hora_final_permiso', 'fecha_registro', 'observacion', 'usuario'], 'safe'],
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
        $query = NovedadOperario::find();

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
            'id_novedad' => $this->id_novedad,
            'id_tipo_novedad' => $this->id_tipo_novedad,
            'id_operario' => $this->id_operario,
            'documento' => $this->documento,
            'fecha_inicio_permiso' => $this->fecha_inicio_permiso,
            'fecha_final_permiso' => $this->fecha_final_permiso,
            'fecha_registro' => $this->fecha_registro,
        ]);

        $query->andFilterWhere(['like', 'hora_inicio_permiso', $this->hora_inicio_permiso])
            ->andFilterWhere(['like', 'hora_final_permiso', $this->hora_final_permiso])
            ->andFilterWhere(['like', 'observacion', $this->observacion])
            ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }
}
