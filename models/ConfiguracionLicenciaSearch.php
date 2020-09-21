<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ConfiguracionLicencia;
use app\models\ConfiguracionLicenciaSearch;

/**
 * ConfiguracionLicenciaSearch represents the model behind the search form of `app\models\ConfiguracionLicencia`.
 */
class ConfiguracionLicenciaSearch extends ConfiguracionLicencia
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo_licencia', 'afecta_salud', 'ausentismo', 'maternidad', 'paternidad', 'suspension_contrato', 'remunerada', 'codigo_salario'], 'integer'],
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
        $query = ConfiguracionLicencia::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
             'sort'=> ['defaultOrder' => ['codigo_licencia' => SORT_ASC]] // Agregar esta linea para agregar el orden por defecto
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'codigo_licencia' => $this->codigo_licencia,
            'afecta_salud' => $this->afecta_salud,
            'ausentismo' => $this->ausentismo,
            'maternidad' => $this->maternidad,
            'paternidad' => $this->paternidad,
            'suspension_contrato' => $this->suspension_contrato,
            'remunerada' => $this->remunerada,
            'codigo_salario' => $this->codigo_salario,
        ]);
        $query->andFilterWhere(['like', 'codigo_salario', $this->codigo_salario]);
        $query->andFilterWhere(['like', 'concepto', $this->concepto]);

        return $dataProvider;
    }
}
