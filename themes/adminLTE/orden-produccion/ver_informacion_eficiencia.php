<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
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
                    Informacion detallada de la operacion(<span class="badge"><?= 1 ?>)</span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <td scope="col" style='background-color:#B9D5CE; '><b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            $contador= 0;
                            foreach ($model as $val): 
                                 $total += $val->porcentaje_cumplimiento; 
                                 $contador++;
                                ?>
                               
                            <?php endforeach; ?>
                        </tbody>    
                    </table>
                    <?php if (!empty($model)){
                       
                       
                        $samPromedio = ($total / $contador);
                     
                        ?>
                        <table class="table table-bordered table-hover" style="margin-left: auto; margin-right: auto;">
                            <tr>
                                
                                <td colspan="4" style="font-size: 100%; background: #277da1; color: #FFFFFF; text-align: center;">
                                    <b>Eficiencia promedio de la operación: </b> <?= round($samPromedio,2) ?> %</b> 
                                </td>

                            </tr>    
                        </table> 
                    <?php } ?>    
                </div>
            </div>
        </div>
    </div>

<?php ActiveForm::end(); ?>
<?php 
// SCRIPT PARA RECARGAR LA PÁGINA AL CERRAR
$script = <<< JS
    // Escucha el evento de cierre de Bootstrap 3/4
    $('.modal').on('hidden.bs.modal', function () {
        window.location.reload();
    });
JS;
$this->registerJs($script);
?>
