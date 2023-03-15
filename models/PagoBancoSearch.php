<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PagoBanco;

/**
 * PagoBancoSearch represents the model behind the search form of `app\models\PagoBanco`.
 */
class PagoBancoSearch extends PagoBanco
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pago_banco', 'nit_empresa', 'id_banco', 'tipo_pago'], 'integer'],
            [['aplicacion', 'secuencia', 'fecha_creacion', 'fecha_aplicacion', 'descripcion', 'usuario'], 'safe'],
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
        $query = PagoBanco::find();

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
            'id_pago_banco' => $this->id_pago_banco,
            'nit_empresa' => $this->nit_empresa,
            'id_banco' => $this->id_banco,
            'tipo_pago' => $this->tipo_pago,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_aplicacion' => $this->fecha_aplicacion,
        ]);

        $query->andFilterWhere(['like', 'aplicacion', $this->aplicacion])
            ->andFilterWhere(['like', 'secuencia', $this->secuencia])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }
}
