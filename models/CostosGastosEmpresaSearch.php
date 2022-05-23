<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CostosGastosEmpresa;

/**
 * CostosGastosEmpresaSearch represents the model behind the search form of `app\models\CostosGastosEmpresa`.
 */
class CostosGastosEmpresaSearch extends CostosGastosEmpresa
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_costo_gasto', 'id', 'total_costo_gasto'], 'integer'],
            [['fecha_inicio', 'fecha_corte', 'fecha_proceso', 'usuariosistema'], 'safe'],
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
        $query = CostosGastosEmpresa::find();

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
            'id_costo_gasto' => $this->id_costo_gasto,
            'fecha_inicio' => $this->fecha_inicio,
            'id' => $this->id,
            'fecha_corte' => $this->fecha_corte,
            'total_costo_gasto' => $this->total_costo_gasto,
            'fecha_proceso' => $this->fecha_proceso,
        ]);

        $query->andFilterWhere(['like', 'usuariosistema', $this->usuariosistema]);

        return $dataProvider;
    }
}
