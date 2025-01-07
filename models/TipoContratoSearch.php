<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TipoContrato;

/**
 * TipoContratoSearch represents the model behind the search form of `app\models\TipoContrato`.
 */
class TipoContratoSearch extends TipoContrato
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tipo_contrato', 'estado', 'prorroga', 'nro_prorrogas', 'id_configuracion_prefijo'], 'integer'],
            [['contrato', 'prefijo','codigo_api_enlace'], 'string'],
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
        $query = TipoContrato::find();

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
            'id_tipo_contrato' => $this->id_tipo_contrato,
            'contrato' => $this->contrato,
            'estado' => $this->estado,
            'prorroga' => $this->prorroga,
            'nro_prorrogas' => $this->nro_prorrogas,
            'prefijo' => $this->prefijo,
            'id_configuracion_prefijo' => $this->id_configuracion_prefijo, 
        ]);

        $query->andFilterWhere(['like', 'id_tipo_contrato', $this->id_tipo_contrato]);
        $query->andFilterWhere(['like', 'contrato', $this->contrato]);
        $query->andFilterWhere(['like', 'estado', $this->estado]);
        $query->andFilterWhere(['like', 'prorroga', $this->prorroga]);
        $query->andFilterWhere(['like', 'nro_prorrogas', $this->nro_prorrogas]);
        $query->andFilterWhere(['like', 'prefijo', $this->prefijo]);
        $query->andFilterWhere(['like', 'id_configuracion_prefijo', $this->id_configuracion_prefijo]);

        return $dataProvider;
    }
}
