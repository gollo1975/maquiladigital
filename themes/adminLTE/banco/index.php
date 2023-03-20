<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $searchModel app\models\BancoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Bancos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bancos-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'idbanco',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [                
                'attribute' => 'nitbanco',
                'contentOptions' => ['class' => 'col-lg-1'],                
            ],
            [               
                'attribute' => 'entidad',
                'contentOptions' => ['class' => 'col-lg-2 '],                
            ],
            [
                'attribute' => 'producto',
                'value' => function($model) {
                    $producto = app\models\Banco::findOne($model->idbanco);
                    return $producto->producto;
                },
                'filter' => ArrayHelper::map(app\models\Banco::find()->all(), 'producto', 'tipoCuenta'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [               
                'attribute' => 'numerocuenta',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'direccionbanco',
                'contentOptions' => ['class' => 'col-lg-2 '],                
            ],
			[               
                'attribute' => 'telefonobanco',
                'contentOptions' => ['class' => 'col-lg-2'],                
            ],			
            [
                'class' => 'yii\grid\ActionColumn',     
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],
			
        ],
        //'tableOptions' => ['class' => 'table table-success'],
        'tableOptions'=>['class'=>'table table-bordered table-success'],        
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


