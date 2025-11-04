<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InventarioPuntoVenta;

/**
 * InventarioPuntoVentaSearch represents the model behind the search form of `app\models\InventarioPuntoVenta`.
 */
class InventarioPuntoVentaSearch extends InventarioPuntoVenta
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inventario', 'codigo_producto', 'costo_unitario', 'stock_unidades', 'stock_inventario', 'idproveedor', 'id_punto', 'id_marca', 'id_categoria', 'iva_incluido', 'inventario_inicial', 'aplica_talla_color', 'aplica_inventario', 'subtotal', 'valor_iva', 'total_inventario', 'precio_deptal', 'precio_mayorista', 'codigo_barra', 'venta_publico', 'aplica_descuento_punto', 'aplica_descuento_distribuidor', 'codigo_enlace_bodega', 'inventario_aprobado', 'stock_minimo'], 'integer'],
            [['nombre_producto', 'descripcion_producto', 'fecha_creacion', 'fecha_proceso', 'user_name'], 'safe'],
            [['porcentaje_iva'], 'number'],
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
        $query = InventarioPuntoVenta::find();

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
            'id_inventario' => $this->id_inventario,
            'codigo_producto' => $this->codigo_producto,
            'costo_unitario' => $this->costo_unitario,
            'stock_unidades' => $this->stock_unidades,
            'stock_inventario' => $this->stock_inventario,
            'idproveedor' => $this->idproveedor,
            'id_punto' => $this->id_punto,
            'id_marca' => $this->id_marca,
            'id_categoria' => $this->id_categoria,
            'iva_incluido' => $this->iva_incluido,
            'inventario_inicial' => $this->inventario_inicial,
            'aplica_talla_color' => $this->aplica_talla_color,
            'aplica_inventario' => $this->aplica_inventario,
            'porcentaje_iva' => $this->porcentaje_iva,
            'subtotal' => $this->subtotal,
            'valor_iva' => $this->valor_iva,
            'total_inventario' => $this->total_inventario,
            'precio_deptal' => $this->precio_deptal,
            'precio_mayorista' => $this->precio_mayorista,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_proceso' => $this->fecha_proceso,
            'codigo_barra' => $this->codigo_barra,
            'venta_publico' => $this->venta_publico,
            'aplica_descuento_punto' => $this->aplica_descuento_punto,
            'aplica_descuento_distribuidor' => $this->aplica_descuento_distribuidor,
            'codigo_enlace_bodega' => $this->codigo_enlace_bodega,
            'inventario_aprobado' => $this->inventario_aprobado,
            'stock_minimo' => $this->stock_minimo,
        ]);

        $query->andFilterWhere(['like', 'nombre_producto', $this->nombre_producto])
            ->andFilterWhere(['like', 'descripcion_producto', $this->descripcion_producto])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
