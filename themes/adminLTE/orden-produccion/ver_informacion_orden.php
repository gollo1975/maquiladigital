<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;

?>


<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Informacion detallada de la OP (<span class="badge"><?= $id ?>)</span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr style='font-size:80%;'>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Producto</td>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Observaciones</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($model as $val): ?>
                                <tr style="font-size: 80%;">
                                    <td><?= $val->tipoProducto->concepto ?></td>  
                                    <td><?= $val->observacion ?></td>  
                                </tr>
                            <?php endforeach; ?>
                        </tbody>    
                    </table>
                   
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
