<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ConceptoDocumentoSoporte;

/**
 * ConceptoDocumentoSoporteSearch represents the model behind the search form of `app\models\ConceptoDocumentoSoporte`.
 */
class ConceptoDocumentoSoporteSearch extends ConceptoDocumentoSoporte
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_concepto'], 'integer'],
            [['concepto', 'codigo_interface', 'user_name'], 'safe'],
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
        $query = ConceptoDocumentoSoporte::find();

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
            'id_concepto' => $this->id_concepto,
        ]);

        $query->andFilterWhere(['like', 'concepto', $this->concepto])
            ->andFilterWhere(['like', 'codigo_interface', $this->codigo_interface])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
