<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipoCompraProcesoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo Compra Procesos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-compra-proceso-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tipo Compra Proceso', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_tipo_compra',
            'descripcion',
            'estado_tipo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
