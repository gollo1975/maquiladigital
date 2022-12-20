<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ComprobanteEgresoTipo;

/**
 * ComprobanteEgresoTipoSearch represents the model behind the search form of `app\models\ComprobanteEgresoTipo`.
 */
class ComprobanteEgresoTipoSearch extends ComprobanteEgresoTipo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_comprobante_egreso_tipo', 'activo','permite_importar'], 'integer'],
            [['concepto'], 'safe'],
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
        $query = ComprobanteEgresoTipo::find();

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
            'id_comprobante_egreso_tipo' => $this->id_comprobante_egreso_tipo,
            'activo' => $this->activo,
            'permite_importar' => $this->permite_importar,
        ]);

        $query->andFilterWhere(['like', 'concepto', $this->concepto]);
        $query->andFilterWhere(['like', 'permite_importar', $this->permite_importar]);

        return $dataProvider;
    }
}
