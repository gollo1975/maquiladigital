<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tiempo por operario';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Fichatiempos-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'id_ficha_tiempo',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'id_operario',
                'value' => function($model){
                    $empleados = app\models\Fichatiempo::findOne($model->id_ficha_tiempo);
                    return "{$empleados->operario->nombrecompleto} - {$empleados->operario->documento}";
                },
                'filter' => ArrayHelper::map(app\models\Operarios::find()->all(),'id_operario','nombrecompleto'),
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [               
                'attribute' => 'referencia',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'desde',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'hasta',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],            
            [               
                'attribute' => 'cumplimiento',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [
                'attribute' => 'estado',
                'value' => function($model){
                    $ficha = \app\models\Fichatiempo::findOne($model->id_ficha_tiempo);                    
                    return $ficha->cerrado;
                },
                'filter' => ArrayHelper::map(\app\models\Fichatiempo::find()->all(),'estado','cerrado'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [               
                'attribute' => 'observacion',
                'contentOptions' => ['class' => 'col-lg-2 '],                
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
