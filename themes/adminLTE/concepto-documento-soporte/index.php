<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\TipoRecibo;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipoReciboSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conceptos DS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiporecibos-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_concepto',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'concepto',
                'contentOptions' => ['class' => 'col-lg-4'],
            ],
             [
                'attribute' => 'codigo_interface',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1'],
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]);
    ?>
</div>