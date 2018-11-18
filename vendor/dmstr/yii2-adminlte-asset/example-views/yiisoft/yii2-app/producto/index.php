<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Cliente;
use app\models\Producto;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="producto-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'idproducto',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'codigoproducto',
                'contentOptions' => ['class' => 'col-lg-1.3'],
            ],
            [
                'attribute' => 'producto',
                'value' => function($model){
                    $productos = Producto::findOne($model->idproducto);
                    return "{$productos->prendatipo->prenda} - {$productos->prendatipo->talla->talla} - {$productos->prendatipo->talla->sexo}";
                },
                'contentOptions' => ['class' => 'col-lg-3.2'],
            ],
            [
                'attribute' => 'cantidad',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'stock',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'idcliente',
                'value' => function($model){
                    $clientes = Cliente::findOne($model->idcliente);
                    return "{$clientes->nombrecorto} - {$clientes->cedulanit}";
                },
                'filter' => ArrayHelper::map(Cliente::find()->all(),'idcliente','nombreClientes'),
                'contentOptions' => ['class' => 'col-lg-4'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
            ],

        ],
        'tableOptions' => ['class' => 'table table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],

    ]); ?>
</div>
