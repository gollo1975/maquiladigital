<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Talla;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PrendatipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Prendas Tipos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prendatipos-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'prenda',
                'contentOptions' => ['class' => 'col-lg-5'],
            ],
            [
                'attribute' => 'idtalla',
                'value' => function($model){
                    $tallas = Talla::findOne($model->idtalla);
                    return "{$tallas->talla} - {$tallas->sexo}";
                    },
                    'filter' => ArrayHelper::map(Talla::find()->all(),'idtalla','tindex'),
                 'contentOptions' => ['class' => 'col-lg-5.5'],
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],

        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
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


