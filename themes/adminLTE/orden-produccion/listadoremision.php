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
                    Lista remisiones <span class="badge"><?= count($model)?></span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive-lg">
                        <thead>
                            <tr style='font-size:90%;'>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Id</td>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Nro remisión</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Op interna</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Op cliente</td>
                                 <td scope="col" style='background-color:#B9D5CE; '><b>Cliente</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Referencia</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Fecha entrega</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Unidades</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($model as $val):?>
                            <tr style="font-size: 95%;">
                                <td><?= $val->id_remision ?></td>  
                                <td><?= $val->numero ?></td>  
                                <td><?= $val->ordenproduccion->idordenproduccion ?></td>
                                  <td><?= $val->ordenproduccion->ordenproduccion ?></td>
                                <td><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                                <td><?= $val->ordenproduccion->codigoproducto ?></td>
                                <td><?= $val->fecha_entrega ?></td>
                                <td style="text-align: right"><?= ''.number_format($val->total_despachadas,0) ?></td>
                                <td style= 'width: 25px; height: 25px;'>
                                    <a href="<?= Url::toRoute(["/remision/remision", "id" => $id, 'token' => $token, 'id_remision' => $val->id_remision]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                </td> 
                            </tr>
                            </tbody>
                            <?php
                        endforeach; ?>                        
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
