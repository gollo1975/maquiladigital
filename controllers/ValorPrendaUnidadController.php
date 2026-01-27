<?php

namespace app\controllers;

use app\models\ValorPrendaUnidad;
use app\models\ValorPrendaUnidadSearch;
use app\models\UsuarioDetalle;
use app\models\Ordenproduccion;
use app\models\ValorPrendaUnidadDetalles;
use app\models\Operarios;
use app\models\FormFiltroValorPrenda;
use app\models\FormFiltroResumePagoPrenda;
use app\models\ModelAplicarPorcentaje;

//clases
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Model;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use Codeception\Lib\HelperModule;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\db\ActiveQuery;
use DateTime;

/**
 * ValorPrendaUnidadController implements the CRUD actions for ValorPrendaUnidad model.
 */
class ValorPrendaUnidadController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all ValorPrendaUnidad models.
     * @return mixed
     */
   public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 106])->all()) {
                $form = new FormFiltroValorPrenda();
                $idtipo = null;
                $idordenproduccion = null;
                $estado_valor = null;
                $cerrar_pago = null;
                $autorizado = null;
                $planta = null;
                $tokenPlanta = null;
                $tokenPlanta = Yii::$app->user->identity->id_planta;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $idtipo = Html::encode($form->idtipo);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $estado_valor = Html::encode($form->estado_valor);
                        $cerrar_pago = Html::encode($form->cerrar_pago);
                        $autorizado = Html::encode($form->autorizado);
                        $planta = Html::encode($form->planta);
                        if($tokenPlanta == null){
                            $table = ValorPrendaUnidad::find()
                                    ->andFilterWhere(['=', 'idtipo', $idtipo])
                                    ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                    ->andFilterWhere(['=', 'estado_valor', $estado_valor])
                                    ->andFilterWhere(['=', 'cerrar_pago', $cerrar_pago])
                                    ->andFilterWhere(['=', 'id_planta', $planta])
                                    ->andFilterWhere(['=', 'autorizado', $autorizado]);
                        }else{
                           $table = ValorPrendaUnidad::find()
                                    ->andFilterWhere(['=', 'idtipo', $idtipo])
                                    ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                    ->andFilterWhere(['=', 'estado_valor', $estado_valor])
                                    ->andFilterWhere(['=', 'cerrar_pago', $cerrar_pago])
                                    ->andFilterWhere(['=', 'id_planta', $tokenPlanta])
                                    ->andFilterWhere(['=', 'autorizado', $autorizado]); 
                        }
                        $table = $table->orderBy('id_valor DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 25,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_valor  DESC']);
                            $this->actionExcelconsultaValorPrenda($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    if($tokenPlanta == null){
                         $table = ValorPrendaUnidad::find()->Where(['=', 'cerrar_pago', 0])->orderBy('id_valor DESC');
                    }else{
                        $table = ValorPrendaUnidad::find()->where(['=','id_planta', $tokenPlanta]) ->orderBy('id_valor DESC');
                    }
                    
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 25,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelconsultaValorPrenda($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'tokenPlanta' => $tokenPlanta,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    
   //INDEX DE LA APP
   public function actionIngreso_eficiencia_empleado() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 165])->all()) {
                $tokenUsuario = Yii::$app->user->identity->username;
                $modelo = null;
                $tokenOperario = null;
                $id_planta = null;
                $tokenPlanta = Operarios::find()->where(['=','documento', Yii::$app->user->identity->username])->one();
                 $tokenPlanta->id_planta;
                if($tokenPlanta){
                    $tokenOperario = $tokenPlanta->id_operario;
                    $id_planta = $tokenPlanta->id_planta;
                    $table = ValorPrendaUnidad::find()->where(['=','id_planta', $tokenPlanta->id_planta])->andWhere(['=','cerrar_pago', 0])->orderBy('id_valor DESC')->all();
                    $modelo = $table;
                }else{
                    Yii::$app->getSession()->setFlash('error', 'El documento del operario no coincide con el usuario ingresado. valide la informacion');
                }
                return $this->render('app_index', [
                      'modelo' => $modelo,  
                      'tokenOperario' =>$tokenOperario,
                      'id_planta' => $id_planta,
                ]); 
            }else{
              return $this->redirect(['site/sinpermiso']);  
            }
        }else{
           return $this->redirect(['site/login']);
        }    
       
       
   }
    
   //index de consulta o pago
    public function actionIndexsoporte() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 107])->all()) {
                $form = new FormFiltroResumePagoPrenda();
                $id_operario = null;
                $dia_pago = '';
                $fecha_corte = '';
                $validar_eficiencia = 0;
                $modelo = null;
                $pages = null;
                $sw = 0;
                $id_planta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $validar_eficiencia = Html::encode($form->validar_eficiencia);
                        $id_planta = Html::encode($form->id_planta);
                        $id_operario = Html::encode($form->id_operario);
                        $dia_pago = Html::encode($form->dia_pago);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        if(empty($dia_pago) || empty($fecha_corte)){
                            Yii::$app->getSession()->setFlash('warning', 'El campo fecha inicio y fecha corte NO pueden ser vacios..');
                            return $this->redirect(['indexsoporte']);
                        }else{
                            if($id_operario <> null ){
                                $sw = 1;
                                $query = ValorPrendaUnidadDetalles::find();
                                $query->joinWith('operarioProduccion');
                                $query->where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                     ->andwhere(['=','valor_prenda_unidad_detalles.id_operario', $id_operario])
                                     ->andWhere(['=','tipo_aplicacion', 0]);
                                $query->orderBy('operarios.nombrecompleto ASC');
                                $table = $query;
                            }else{
                                $sw = 2;
                                if($id_planta <> null ){
                                    $query = ValorPrendaUnidadDetalles::find();
                                    $query->joinWith('operarioProduccion');
                                    $query->where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                          ->andWhere(['=', 'valor_prenda_unidad_detalles.id_planta', $id_planta])
                                          ->andWhere(['=','tipo_aplicacion', 0]);
                                    $query->orderBy('operarios.nombrecompleto ASC, consecutivo DESC');
                                    $table = $query;
                                }else{
                                   Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar el OPERARIO o la PLANTA DE PRODUCCION');
                                   return $this->redirect(['indexsoporte']);
                                 }    
                            }    
                        }
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 120,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['consecutivo  DESC']);
                            $this->actionExcelResumeValorPrenda($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                }             
                return $this->render('indexsoporte', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'sw' => $sw,
                            'pagination' => $pages,
                            'validar_eficiencia' => $validar_eficiencia,
                            'dia_pago' =>$dia_pago,
                            'fecha_corte' => $fecha_corte,
                            'id_operario' => $id_operario,
                            'id_planta' => $id_planta,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    
    //index de consulta o pago
    public function actionValor_prenda_app() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 167])->all()) {
                $form = new FormFiltroResumePagoPrenda();
                $id_operario = null;
                $dia_pago = '';
                $fecha_corte = '';
                $validar_eficiencia = 0;
                $modelo = null;
                $pages = null;
                $sw = 0;
                $id_planta = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $validar_eficiencia = Html::encode($form->validar_eficiencia);
                        $id_planta = Html::encode($form->id_planta);
                        $id_operario = Html::encode($form->id_operario);
                        $dia_pago = Html::encode($form->dia_pago);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        if(empty($dia_pago) || empty($fecha_corte)){
                            Yii::$app->getSession()->setFlash('warning', 'El campo fecha inicio y fecha corte NO pueden ser vacios..');
                            return $this->redirect(['valor_prenda_app']);
                        }else{
                            if($id_operario != null ){
                                $sw = 1;
                                $query = ValorPrendaUnidadDetalles::find()
                                    ->joinWith('operarioProduccion')
                                    ->where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                    ->andWhere(['valor_prenda_unidad_detalles.id_operario' => $id_operario])
                                    ->andWhere(['valor_prenda_unidad_detalles.tipo_aplicacion' => 1]);

                                $query->orderBy('operarios.nombrecompleto ASC, valor_prenda_unidad_detalles.consecutivo DESC');

                                $table = $query;
                            }else{
                                $sw = 2;
                                if($id_planta != null ){
                                    $query = ValorPrendaUnidadDetalles::find();
                                    $query->joinWith('operarioProduccion');
                                    $query->where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                            ->andWhere(['valor_prenda_unidad_detalles.id_planta' => $id_planta])
                                            ->andWhere(['valor_prenda_unidad_detalles.tipo_aplicacion' => 1]);
                                    $query->orderBy('operarios.nombrecompleto ASC, consecutivo DESC');
                                    $table = $query;
                                }else{
                                   Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar el OPERARIO o la PLANTA DE PRODUCCION');
                                   return $this->redirect(['valor_prenda_app']);
                                 }    
                            }    
                        }
                        
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 120,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['consecutivo  DESC']);
                            $this->actionExcelResumeValorPrenda($tableexcel);
                        }
                        
                    } else {
                        $form->getErrors();
                    }
                }    
               
               return $this->render('valor_prenda_app', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'sw' => $sw,
                            'pagination' => $pages,
                            'validar_eficiencia' => $validar_eficiencia,
                            'dia_pago' =>$dia_pago,
                            'fecha_corte' => $fecha_corte,
                            'id_operario' => $id_operario,
                            'id_planta' => $id_planta,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    
    //EFICIENCIA DIARIA POR FECHAS
    //index de consulta o pago
    public function actionEficiencia_diaria() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 134])->all()) {
                $form = new FormFiltroResumePagoPrenda();
                $id_operario = null;
                $dia_pago = null;
                $fecha_corte = null;
                $id_planta = null;
                $modelo = null;
                $sw = 0;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $dia_pago = Html::encode($form->dia_pago);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $id_planta = Html::encode($form->id_planta);
                        if($dia_pago == null && $fecha_corte == null){
                            Yii::$app->getSession()->setFlash('warning', 'El campo fecha inicio y fecha corte NO pueden ser vacios..');
                            return $this->redirect(['eficiencia_diaria']);
                        }else{
                            if($id_operario <> null ){
                                $sw = 1;
                                $table = ValorPrendaUnidadDetalles::find()
                                        ->Where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                        ->andWhere(['=','id_operario', $id_operario])
                                        ->orderBy('dia_pago DESC')->all();
                            }else{
                                $sw = 2;
                                if($id_planta <> null ){
                                    $table = ValorPrendaUnidadDetalles::find()
                                        ->Where(['between', 'dia_pago', $dia_pago, $fecha_corte])
                                        ->andWhere(['=','id_planta', $id_planta])
                                        ->orderBy('id_operario ASC')->all();
                                }else{
                                   Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar el OPERARIO o la PLANTA DE PRODUCCION');
                                   return $this->redirect(['eficiencia_diaria']);
                                 }    
                            }    
                            $modelo = $table;     
                        }  
                        
                    } else {
                        $form->getErrors();
                    }
                }             
                return $this->render('eficiencia_diaria', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'dia_pago' =>$dia_pago,
                            'fecha_corte' => $fecha_corte,
                            'id_operario' => $id_operario,
                            'id_planta' => $id_planta,
                            'sw' => $sw,
                            'dia_pago' => $dia_pago,
                            'fecha_corte' => $fecha_corte,
                            ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    } 
       
    //PERMITE CONSULTAR LOS PAGOS DE SERVICIOS
    
    public function actionSearchpageprenda() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 119])->all()) {
                $form = new \app\models\FormFiltroSearchPagePrenda();
                $id_operario = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $documento = NULL;  
                $planta = null;
                $tokenPlanta = null;
                $tokenPlanta = Yii::$app->user->identity->id_planta;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $id_operario = Html::encode($form->id_operario);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $documento = Html::encode($form->documento);
                        $planta = Html::encode($form->planta);
                        if($tokenPlanta == null){
                            $table = \app\models\PagoNominaServicios::find()
                                    ->andFilterWhere(['=', 'id_operario', $id_operario])
                                    ->andFilterWhere(['between', 'fecha_inicio', $fecha_inicio, $fecha_corte])
                                    ->andFilterWhere(['=', 'documento', $documento])
                                    ->andFilterWhere(['=', 'id_planta', $planta])
                                    ->andWhere(['=','autorizado', 1]);
                        }else{
                            $table = \app\models\PagoNominaServicios::find()
                                    ->andFilterWhere(['=', 'id_operario', $id_operario])
                                    ->andFilterWhere(['between', 'fecha_inicio', $fecha_inicio, $fecha_corte])
                                    ->andFilterWhere(['=', 'documento', $documento])
                                    ->andFilterWhere(['=', 'id_planta', $tokenPlanta])
                                    ->andWhere(['=','autorizado', 1]);
                        }
                        $table = $table->orderBy('id_pago DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 120,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_pago  DESC']);
                            $this->actionExcelPagoPrendaServicio($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    if($tokenPlanta == null){
                        $table = \app\models\PagoNominaServicios::find()->where(['=','autorizado', 1])
                                 ->orderBy('id_pago DESC');
                    }else{
                        $table = \app\models\PagoNominaServicios::find()->where(['=','autorizado', 1])->andWhere(['=','id_planta', $tokenPlanta])
                                 ->orderBy('id_pago DESC'); 
                    }    
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 120,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelPagoPrendaServicio($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('searchpageprenda', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'tokenPlanta' => $tokenPlanta,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    } 
    
    //PROCESO QUE MUESTRAS LA BITACORA
    public function actionBitacora_eficiencia()
    {
        if (!Yii::$app->user->identity) {
            return $this->redirect(['site/login']);
        }

        // Verificación de permisos
        $permiso = UsuarioDetalle::find()
            ->where(['codusuario' => Yii::$app->user->identity->codusuario, 'id_permiso' => 190])
            ->exists();

        if (!$permiso) {
            return $this->redirect(['site/sinpermiso']);
        }

        $form = new \app\models\FormFiltroBitacora();
        $conOperaciones = [];
        $model = [];

        // Capturar datos del GET
        $form->load(Yii::$app->request->get());

        // 1. Preparar la consulta base
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                
                // 2. Aplicar filtros dinámicos
                $table = \app\models\BitacoraEficienciaOperario::find()
                      ->andFilterWhere(['=', 'id_operario', $form->operario])
                      ->andFilterWhere(['between', 'fecha_confeccion', $form->desde, $form->hasta])
                      ->andFilterWhere(['between', 'hora_corte', $form->inicio_hora_corte, $form->final_hora_corte])
                      ->andFilterWhere(['=', 'idordenproduccion', $form->orden_produccion])
                      ->andFilterWhere(['=', 'idproceso', $form->operacion]); // Filtro por operación si existe
                    if ($form->valores == 0){ //positivos
                        $table = $table->andFilterWhere(['>', 'porcentaje_eficiencia', $form->valores]);
                    }else{
                       $table = $table->andFilterWhere(['<', 'porcentaje_eficiencia', $form->valores]); 
                    }  
                $table->orderBy([
                            'id' => SORT_DESC,
                            'id_operario' => SORT_ASC
                        ]);
                
                $model = $table->all();
                $tableexcel = $model;
                if (isset($_POST['excel'])) {
                    $check = isset($_REQUEST['id  DESC']);
                    $this->actionExcelconsultaBitacora($tableexcel);
                }

                // Cargar operaciones si hay una orden seleccionada para que el dropdown no se vacíe
                if (!empty($form->orden_produccion)) {
                    $conOperaciones = ArrayHelper::map(
                        \app\models\FlujoOperaciones::find()
                            ->where(['idordenproduccion' => $form->orden_produccion])
                            ->all(), 
                        'idproceso', 
                        'mostrarOperacion'
                    );
                }
            }

            // 3. Implementar Paginación (necesaria para tu vista)
            
        } 
        // 4. SIEMPRE retornar el render al final para que la vista se mantenga
        return $this->render('bitacora_eficiencia', [
            'model' => $model,
            'form' => $form,
            'conOperaciones' => $conOperaciones,
            
        ]);
    }
    
    public function actionControl_linea_confeccion() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 159])->all()) {
                $form = new \app\models\FormFiltroControlLinea();
                $operario = null;
                $desde = null;
                $hasta = null; $model = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $operario = Html::encode($form->operario);
                        $desde = Html::encode($form->desde);
                        $hasta = Html::encode($form->hasta);
                        if($hasta <> null && $desde <> null && $hasta <> null){
                            $table = ValorPrendaUnidadDetalles::find()
                                        ->andFilterWhere(['=', 'id_operario', $operario])
                                       ->andFilterWhere(['between', 'dia_pago', $desde, $hasta]);
                            $table = $table->orderBy('consecutivo DESC');
                            $model = $table->all();
                        }else{
                            Yii::$app->getSession()->setFlash('error', 'Todos los campos deben de estar seleccionados. Vuelva a intentarlo.');
                                                  }    
                    }else{
                        $form->getErrors();
                    }    
                }
                return $this->render('control_linea_confeccion', [
                            'model' => $model,
                            'form' => $form,
                ]);
           } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }     
    }
    
    //PERMITE BUSCAR LOS INGRESOS OPERATIVOS POR DIA, OPERARIO, PLANTA
    public function actionCosto_gasto_operario() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 145])->all()) {
                $form = new FormFiltroValorPrenda();
                $operario = null;
                $idordenproduccion = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $operacion = null;
                $planta = null;
                $modelo = null;
                $pages = null;
                $tableexcel = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $operario = Html::encode($form->operario);
                        $idordenproduccion = Html::encode($form->idordenproduccion);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $operacion = Html::encode($form->operacion);
                        $planta = Html::encode($form->planta); 
                        if($fecha_inicio == '' && $fecha_corte == ''){
                            Yii::$app->getSession()->setFlash('warning', 'Los campos Fecha de ingreso y de retiro no ser vacios. Vuelva a intentarlo.');
                            return $this->redirect(['costo_gasto_operario']);
                        }else{
                            $table = ValorPrendaUnidadDetalles::find()
                                        ->andFilterWhere(['=', 'id_operario', $operario])
                                        ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                                        ->andFilterWhere(['between', 'dia_pago', $fecha_inicio, $fecha_corte])
                                        ->andFilterWhere(['=', 'idproceso', $operacion])
                                        ->andFilterWhere(['=', 'id_planta', $planta]);
                            if($operario <> ''){
                               $table = $table->orderBy('dia_pago DESC');  
                            }else{
                                 $table = $table->orderBy('id_operario, dia_pago DESC' ); 
                            }                      
                            $tableexcel = $table->all();
                            $count = clone $table;
                            $to = $count->count();
                            $pages = new Pagination([
                                'pageSize' => 20,
                                'totalCount' => $count->count()
                            ]);
                            $modelo = $table
                                    ->offset($pages->offset)
                                    ->limit($pages->limit)
                                    ->all();
                            if (isset($_POST['excel'])) {
                                $check = isset($_REQUEST['consecutivo  DESC']);
                                $this->actionExcelConsultaIngresos($tableexcel);
                            }
                        }    
                    } else {
                        $form->getErrors();
                    }
                } 
                return $this->render('index_ingresos', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                            'tableexcel' => $tableexcel,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }
    //ABRE Y CIERRA EL REGISTRO
    public function actionCerrarAbrirRegistro($codigo) {
        $detalle = ValorPrendaUnidadDetalles::findOne($codigo);
        if($detalle->exportado == 0){
          $detalle->exportado = 1;
        }else{
            $detalle->exportado = 0;
        }  
        $detalle->save(false);
    }
    
   //VISTA
    public function actionView($id, $idordenproduccion, $id_planta, $tipo_pago)
    {
        $detalles_pago = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->orderBy('consecutivo desc')->all();
        //proceso para actualizar
           if (!isset($_POST["actualizarlinea"])) { 
                if (isset($_POST["detalle_pago_prenda"])) {
                    $intIndice = 0;
                    $salario = 0;
                    $auxiliar = 0;
                    $maestroValor = ValorPrendaUnidad::findOne($id);
                    foreach ($_POST["detalle_pago_prenda"] as $intCodigo) { 
                        $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                        $table->id_operario = $_POST["id_operario"][$intIndice];
                        $table->operacion = $_POST["operacion"][$intIndice];
                        $table->dia_pago = $_POST["dia_pago"][$intIndice];
                        $table->cantidad = $_POST["cantidad"][$intIndice];
                        $table->control_fecha = $_POST["control_fecha"][$intIndice];
                        $table->aplica_sabado = $_POST["aplica_sabado"][$intIndice];
                        $auxiliar = $table->control_fecha;
                        $operario = Operarios::find()->where(['=','id_operario', $_POST["id_operario"][$intIndice]])->one();
                        $valor_unidad = ValorPrendaUnidad::find()->where(['=','id_valor', $id])->andWhere(['=','idordenproduccion', $idordenproduccion])->one();
                        $vlr_unidad = 0; 
                        if($table->aplica_regla == 0){
                            if($operario){
                                $conMatricula = \app\models\Matriculaempresa::findOne(1);
                                if($operario->vinculado == 1){
                                   $vlr_unidad = $valor_unidad->vlr_vinculado;
                                    if($_POST["vlr_prenda"][$intIndice] == ''){
                                       $table->vlr_prenda = $vlr_unidad;
                                       if($valor_unidad->debitar_salario_dia == 1){ 
                                           $salario = round($operario->salario_base /30);
                                           $table->vlr_pago = (($table->vlr_prenda * $table->cantidad) - $salario);
                                           $this->CostoOperarioVinculado($table, $auxiliar);
                                       }else{
                                           $table->vlr_pago = $table->vlr_prenda * $table->cantidad;
                                           $this->CostoOperarioVinculado($table, $auxiliar);
                                       }    
                                    }else{
                                        $table->vlr_prenda = $_POST["vlr_prenda"][$intIndice];
                                        if($valor_unidad->debitar_salario_dia == 1){ 
                                            $salario = round($operario->salario_base /30);
                                            $table->vlr_pago = (($table->vlr_prenda * $table->cantidad) - $salario);
                                            $this->CostoOperarioVinculado($table, $auxiliar);
  
                                        }else{
                                            $table->vlr_pago = $_POST["vlr_prenda"][$intIndice] * $table->cantidad; 
                                           $this->CostoOperarioVinculado($table, $auxiliar); 

                                        }    
                                    }
                                    //calculo para hallar el % de cumplimiento
                                    $balanceoModulo= \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
                                    $totalHoras = 0; $totalTiempo = 0; $total_diario = 0;
                                    $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos =0; $metaDiaria = 0;
                                    $cumplimiento = 0;
                                    foreach ($balanceoModulo as $modulo):
                                         if($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo >  $operario->horarios->desde){
                                             $horad = explode(":", $table->hora_inicio_modulo);
                                             $horah = explode(":", $operario->horarios->hasta);
                                             $sumarh = $horah[0] - $horad[0];
                                             $sumarm = $horah[1] + $horad[1];
                                             $totalTiempo = $sumarh;
                                             $totalTiempo = ($sumarh * 60) + $sumarm;
                                             $totalTiempo = $totalTiempo/60;
                                             $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado; 
                                             $total_diario = round((60/$can_minutos)* $totalTiempo,0);
                                             $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                             $metaDiaria = round((((60/$can_minutos)* $totalTiempo) * $conMatricula->porcentaje_empresa)/100); 
                                             $sw = 1;
                                         }                                                          
                                    endforeach;
                                    if ($sw == 0){
                                         $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado; 
                                         $total_diario = round((60/$can_minutos)* $operario->horarios->total_horas,0);
                                         $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                         $metaDiaria = round((((60/$can_minutos)* $operario->horarios->total_horas) * $conMatricula->porcentaje_empresa)/100);
                                     }
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->observacion = 'Vinculado';
                                    $table->porcentaje_cumplimiento = $cumplimiento;
                                    $table->meta_diaria = $metaDiaria;
                                    if($maestroValor->tipo_proceso_pago == 2){
                                    $table->aplica_regla = 1;    
                                    }
                                    $table->save(false);
                                    $intIndice++;
                                }else{
                                   $vlr_unidad = $valor_unidad->vlr_contrato; 
                                   if($_POST["vlr_prenda"][$intIndice] == ''){
                                        $table->vlr_prenda = $vlr_unidad;
                                        $table->vlr_pago = $table->vlr_prenda * $table->cantidad;
                                        $table->costo_dia_operaria = $table->vlr_pago;
                                   }else{
                                        $table->vlr_prenda = $_POST["vlr_prenda"][$intIndice];
                                        $table->vlr_pago = $_POST["vlr_prenda"][$intIndice] * $table->cantidad; 
                                        $table->costo_dia_operaria = $table->vlr_pago;
                                   }
                                   //calculo para hallar el % de cumplimiento
                                    $balanceoModulo= \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
                                    $totalHoras = 0; $totalTiempo = 0; $total_diario = 0;
                                    $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos =0; $metaDiaria = 0;
                                    $cumplimiento = 0;
                                    foreach ($balanceoModulo as $modulo):
                                         if($table->dia_pago == $modulo->fecha_inicio && $table->hora_inicio_modulo > $operario->horarios->desde){
                                             $horad = explode(":", $table->hora_inicio_modulo);
                                             $horah = explode(":", $operario->horarios->hasta);
                                             $sumarh = $horah[0] - $horad[0];
                                             $sumarm = $horah[1] + $horad[1];
                                             $totalTiempo = ($sumarh * 60) + $sumarm;
                                             $totalTiempo = $totalTiempo/60;
                                             $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato; 
                                             $total_diario = round((60/$can_minutos)* $totalTiempo,0);
                                             $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                             $metaDiaria = round((((60/$can_minutos)* $totalTiempo) * $conMatricula->porcentaje_empresa)/100); 
                                             $sw = 1;
                                         }
                                    endforeach;
                                    if($sw == 0){
                                        $can_minutos = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato; 
                                        $total_diario = round((60/$can_minutos)* $operario->horarios->total_horas,0);
                                        $cumplimiento = round(($table->cantidad / $total_diario)*100, 2);
                                        $metaDiaria = round((((60/$can_minutos)* $operario->horarios->total_horas) * $conMatricula->porcentaje_empresa)/100);
                                    }    
                                    $table->usuariosistema = Yii::$app->user->identity->username;
                                    $table->observacion = 'No vinculado';
                                    $table->porcentaje_cumplimiento = $cumplimiento;
                                    $table->meta_diaria = $metaDiaria;
                                    if($maestroValor->tipo_proceso_pago == 2){
                                       $table->aplica_regla = 1;    
                                    }
                                    $table->save(false);
                                    $intIndice++;
                               }  
                            }
                        }
                    }
                    $this->Totalpagar($id);
                    $this->TotalCantidades($id, $tipo_pago);
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
                }
            } 
           
     return $this->render('view', [
            'model' => $this->findModel($id),
            'idordenproduccion' => $idordenproduccion,
            'detalles_pago' => $detalles_pago,
            'id_planta' =>$id_planta,
            'tipo_pago' => $tipo_pago,
        ]);
    }
    
    //VISTA PARA PASARA LA TALLAS DE LA OP
    public function actionView_produccion($id, $idordenproduccion, $id_planta, $tokenOperario){
        $detalle_orden = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','id_planta', $id_planta])->all();
       
        //buscamos si hay hora y corte creado
        $buscaHora = \app\models\HoraCorteEficienciaApp::find()->where(['=','fecha_dia', date('Y-m-d')])
                              ->andWhere(['=','idordenproduccion', $idordenproduccion])->one();
        if($buscaHora){
            return $this->render('view_app_produccion', [
                'model' => $this->findModel($id),
                'idordenproduccion' => $idordenproduccion,
                'detalle_orden' => $detalle_orden,
                'id_planta' =>$id_planta,
                'id' => $id,
                'tokenOperario' => $tokenOperario,
            ]); 
        }else{
           Yii::$app->getSession()->setFlash('error', 'No se ha creado la hora y la fecha de corte para la orden de produccion No ('.$idordenproduccion.'). Valide la informacion con el administrador.'); 
           return $this->redirect(['ingreso_eficiencia_empleado']);
        }    
    }
    
    //VISTA DE PERMITE MODIFICAR LA HORA DE CORTE
    public function actionView_edit_hora($id_valor)
    {
        $model = ValorPrendaUnidad::findOne($id_valor);
        $fechaHoy = date('Y-m-d');
        $listado = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $id_valor])->andWhere(['=','fecha_proceso', $fechaHoy])->orderBy('id_corte DESC')->one();    
        if(isset($_POST["actualizar_hora"])){
            if(isset($_POST['listado_hora'])){
                $intIndice = 0;
                foreach ($_POST["listado_hora"] as $intCodigo):
                    $hora_inicio = $_POST["hora_inicio"][$intIndice];
                    $hora_corte = $_POST["hora_corte"][$intIndice];
                    if($hora_inicio <> '' && $hora_corte <> ''){
                        $table = \app\models\ValorPrendaCorteConfeccion::findOne ($intCodigo);
                        if($table){
                            $table->hora_inicio = $hora_inicio;
                            $table->hora_corte = $hora_corte;
                            $table->save();
                            $intIndice++;    
                        }
                    }else{
                        Yii::$app->getSession()->setFlash('warning', 'La hora de inicio y la hora de corte no ser vacias.');
                    }    
                endforeach;  
                return $this->redirect(['view_edit_hora', 'listado' => $listado, 'id_valor' => $id_valor, 'model' => $model]);
            }
        }    
        return $this->render('edit_fecha_corte', ['listado' => $listado, 'id_valor' => $id_valor, 'model' => $model]);
        
    }
    
    public function actionCantidad_talla_confeccion($idordenproduccion, $id, $id_planta, $id_detalle,$tokenPlanta, $tipo_pago) {
        $talla = \app\models\Ordenproducciondetalle::findOne($id_detalle);
        $detalle_op = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $id_detalle])->one();
        
        $listado_confeccion = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $idordenproduccion])
                                                               ->andWhere(['=','id_valor', $id])
                                                               ->andWhere(['=','iddetalleorden', $id_detalle]);
        $listado_confeccion = $listado_confeccion->orderBy('consecutivo DESC');
        $tableexcel = $listado_confeccion->all();
        $count = clone $listado_confeccion;
        $to = $count->count();
        $pages = new Pagination([
            'pageSize' => 20,
            'totalCount' => $count->count()
        ]);
        $listado_confeccion = $listado_confeccion
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        if(count($listado_confeccion) > 0){
            $this->TotalizarCostoTallas($id, $id_detalle);
            return $this->render('maestro_cantidad_talla', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'id_detalle' => $id_detalle,
                'talla' => $talla,
                'id' => $id,
                'listado_confeccion' => $listado_confeccion,
                'detalle_op' => $detalle_op,
                'pagination' => $pages,
                'tokenPlanta' => $tokenPlanta,
                'tipo_pago' => $tipo_pago,
            ]);
        }else{
            Yii::$app->getSession()->setFlash('warning', 'No hay registros para mostrar de esta talla.');
            return $this->redirect(['search_tallas_ordenes','id' => $id,'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago, 'tokenPlanta' => $tokenPlanta, 'idordenproduccion' =>$idordenproduccion]);
        }            
            return $this->render('search_tallas_ordenes', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'conTallas' =>  $conTallas,
                'tokenPlanta' => $tokenPlanta,
                'orden' => $orden,

            ]);
    }
    
   //VISTA QUE TRAE LAS OPERACIONES DE LA OP
    public function actionView_search_operaciones($idordenproduccion, $id, $id_planta, $codigo, $tokenPlanta, $tipo_pago){
        $form = new \app\models\ModeloBuscarOperario();
        $operario = null;
        $detalle_balanceo = 0;
        $fecha_entrada = null;
        $aplica_sabado = null; $alimentacion = null;
        $modulo = null;
        $id_detalle = null;
        $hora_inicio = null;
        $hora_corte = null;
        $conOperaciones = null;
        $nombre_modulo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','id_planta', $id_planta])->all();
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $conCorteProceso = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $id])->orderBy('id_corte DESC')->one();
        if($tokenPlanta == 0){
          $listado_tallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->all();  
        }else{
           $listado_tallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])->andWhere(['=','id_planta', $id_planta])->all(); 
        }
        
        if ($form->load(Yii::$app->request->get())) {
            $operario = Html::encode($form->operario);
            $aplica_sabado = Html::encode($form->aplica_sabado);
            $modulo = Html::encode($form->modulo);
            $fecha_entrada = Html::encode($form->fecha_entrada);
            $alimentacion = Html::encode($form->alimentacion);
            $id_detalle = Html::encode($form->id_detalle);
            if($conCorteProceso){
               $hora_corte = $conCorteProceso->hora_corte;
               $hora_inicio = $conCorteProceso->hora_inicio;
            }
            //CODIGO QUE BUSCA ENTRADAS REGISTRADAS
            if (empty($operario) || empty($fecha_entrada) || empty($id_detalle) || empty($hora_inicio) || empty($hora_corte) || empty($modulo)) {
                Yii::$app->getSession()->setFlash('warning', 'Debe seleccionar el OPERARIO, FECHA, NOMBRE DEL MODULO, HORA INICIO Y HORA CORTE para la búsqueda. Algunos campos están vacíos.');
                return $this->redirect(['view_search_operaciones', 'id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' => $id, 'id_detalle' => $id_detalle, 'codigo' => $codigo, 'tokenPlanta' => $tokenPlanta, 'tipo_pago' => $tipo_pago]);
            }
            $conOperaciones = ValorPrendaUnidadDetalles::find()
                ->where(['=', 'id_valor', $id])
                ->andWhere(['=', 'dia_pago', $fecha_entrada])
                ->andWhere(['=', 'id_operario', $operario])
                ->orderBy('hora_inicio DESC')
                ->all();

            $conCrearCorte = \app\models\ValorPrendaCorteConfeccion::find()
                ->where(['=', 'id_valor', $id])
                ->andWhere(['=', 'fecha_proceso', $fecha_entrada])
                ->one();

            if ($conCrearCorte) {
                $detalle_balanceo = \app\models\BalanceoDetalle::find()
                    ->where(['=', 'id_operario', $operario])
                    ->andWhere(['=', 'idordenproduccion', $idordenproduccion])
                    ->andWhere(['=', 'estado_operacion', 0])
                    ->andWhere(['=', 'id_balanceo', $modulo])
                    ->all();
            } else {
                Yii::$app->getSession()->setFlash('error', 'La FECHA DE CONFECCION no puede estar vacía. Valide la información.');
            }
            
        }
        if (isset($_POST["envia_dato_confeccion"])) {
            if ($fecha_entrada != null) {
                $intIndice = 0; $cantidad = 0;
                foreach ($_POST["operaciones"] as $intCodigo) {
                    if ($_POST["cantidad"][$intIndice] > 0){
                        $total_unidades = 0; $cant = 0; $confeccionada = 0; $sumar_unidades = 0; $total_operacion = 0; $total_unidades_faltante = 0;
                        $conCantidad = \app\models\Ordenproducciondetalle::findOne($id_detalle);
                        $detalle = \app\models\BalanceoDetalle::findOne($intCodigo);//busca las operaciones en el balanceo
                        $detalle_valor_prenda = ValorPrendaUnidadDetalles::find()->where(['=','idproceso', $detalle->id_proceso])->andWhere(['=','iddetalleorden', $id_detalle])->all();
                        $balanceo_entrada = \app\models\Balanceo::findOne($modulo);
                        foreach ($detalle_valor_prenda as $detalle_valor):
                            $sumar_unidades += $detalle_valor->cantidad;
                        endforeach;
                        
                        $total_unidades_faltante = $conCantidad->cantidad - $sumar_unidades; //totaliza las uniades faltantes
                        $cant = $_POST["cantidad"][$intIndice];
                        $confeccionada = $conCantidad->cantidad_confeccionada;
                        $total_unidades =  $confeccionada + $cant;
                        $total_operacion = intval($sumar_unidades) + $cant;
                        if ($total_operacion <= $conCantidad->cantidad){ //ciclo que valide no ingresar mas de las opraciones de la talla
                            if($total_unidades <= $conCantidad->cantidad_operaciones){ //valida todas la operaciones de la talla
                                $valor_prenda = 0; $nota = '';
                                $tipo_proceso = ValorPrendaUnidad::findOne($id);
                                $operarios = Operarios::findOne($detalle->id_operario);//busca el operario
                                $con = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario])->andWhere(['=','dia_pago', $fecha_entrada])->one();
                                $con_hora_repetida = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario])->andWhere(['=','dia_pago', $fecha_entrada])
                                                                                      ->andWhere(['=','hora_corte', $hora_corte])->orderBy('consecutivo DESC')->one(); //permite descontar un hora de entrada
                                $table = new ValorPrendaUnidadDetalles();
                                $table->id_operario = $operario;
                                $table->idordenproduccion = $idordenproduccion;
                                $table->operacion = 1;
                                $table->hora_inicio_modulo = $balanceo_entrada->hora_inicio;
                                $table->dia_pago = $fecha_entrada;
                                $cantidad = $_POST["cantidad"][$intIndice];
                                $table->minuto_prenda = $detalle->minutos;
                                $table->cantidad = $_POST["cantidad"][$intIndice];
                                $valor_minuto =  $detalle->ordenProduccion->cliente->minuto_confeccion;
                                if($valor_minuto){
                                   $table->total_valor_venta = round($valor_minuto * $detalle->minutos) * $cantidad; 
                                }else{
                                   $table->total_valor_venta = 0;  
                                }
                                //valida si el operario es vinculado o al contrato
                                if($operarios->vinculado == 0){ // operaria al contrato
                                    $valor_prenda = round($detalle->minutos * $empresa->vlr_minuto_contrato);
                                    $nota = 'Contrato';
                                    $valor_costo = $valor_prenda * $cantidad;
                                    $table->costo_dia_operaria = $valor_costo;
                                    if(!$con_hora_repetida){
                                        $table->hora_descontar = 1;
                                    }
                                }else{ // operaria vinculada
                                    $valor_prenda = round($detalle->minutos * $empresa->vlr_minuto_vinculado);
                                    $nota = 'Vinculado';
                                    if($con){
                                        $table->control_fecha = 1;
                                    }
                                    if(!$con_hora_repetida){
                                        $table->hora_descontar = 1;
                                    }
                                }
                                $table->vlr_prenda = $valor_prenda;
                                $table->vlr_pago = $valor_prenda * $cantidad;
                                $table->id_valor = $id;
                                $table->usuariosistema = Yii::$app->user->identity->username;
                                $table->observacion = $nota;
                                $table->id_planta = $id_planta;
                                $table->id_tipo = $tipo_proceso->idtipo;
                                $table->aplica_regla = 1;
                                $table->alimentacion = $alimentacion;
                                if($aplica_sabado == 1){
                                    $table->aplica_sabado = 1;
                                }
                                $table->iddetalleorden = $id_detalle;
                                $table->idproceso = $detalle->id_proceso;
                                $table->hora_inicio = $_POST["hora_inicio"][$intIndice];
                                $table->hora_corte = $hora_corte;
                                $table->save(false);
                                $this->SumarCantidadCostoConfeccion($id, $id_detalle, $idordenproduccion);
                                $this->CalcularEficienciaOperario($operario, $idordenproduccion, $id, $id_detalle);
                            }else{
                                Yii::$app->getSession()->setFlash('error', 'No se puede ingresar mas operaciones porque supera el maximo de unidades, favor validar que operaciones faltaron por ingresar.');
                            }    
                        }else{
                            Yii::$app->getSession()->setFlash('info', 'No se puede ingresar mas operaciones del codigo ('.$detalle->id_proceso.') porque supera la cantidad de prendas. Cantidad de prendas: ('.$conCantidad->cantidad.'), cantidad faltante: ('. $total_unidades_faltante .'). Favor validar la informacion de ingreso.');
                        }
                       
                    }
                    $intIndice++;  
               }// cirra el foreach 
            }else{
                Yii::$app->getSession()->setFlash('info', 'Debe de seleccionar la fecha de confeccion de la lista para enviar la información.');
                
            }   
        }
        $conOperaciones = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->andWhere(['=','dia_pago', $fecha_entrada])
                                                           ->andWhere(['=','id_operario', $operario])->orderBy('hora_inicio DESC')->all();
         return $this->render('view_search_operario', [
            'model' => $this->findModel($id),
            'id_planta' =>$id_planta,
            'form' => $form,
            'detalle_balanceo' =>  $detalle_balanceo,
            'id_detalle' => $id_detalle,
            'empresa' => $empresa,
            'codigo' => $codigo,
            'tokenPlanta' => $tokenPlanta,
            'tipo_pago' => $tipo_pago,
            'conCorteProceso' => $conCorteProceso, 
            'conOperaciones' => $conOperaciones,
            'nombre_modulo' => ArrayHelper::map($nombre_modulo, "id_balanceo", "nombreBalanceo"),
            'listado_tallas' => ArrayHelper::map($listado_tallas, "iddetalleorden", "listadoTalla"),
          
        ]);
    }
    
    //PERMITE ENVIAR LA OPERACION AL SISTEMA PARA CALIFICAR LA EFICIENCIA
    public function actionEnviar_operacion_individual($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle, $id_operacion)
    {
       
        $flujo = \app\models\FlujoOperaciones::find()->where(['idproceso' => $id_operacion, 'idordenproduccion' => $idordenproduccion])->one();

        $operario = \app\models\Operarios::findOne($tokenOperario);

        // Si no se encuentra el flujo, redirigir con un mensaje de error
        if (!$flujo || !$operario) {
            Yii::$app->getSession()->setFlash('error', 'Error de comunicación o el código de operación no se encontró en el balanceo.');
            return $this->redirect([
                'entrada_operacion_talla',
                'id_planta' => $id_planta,
                'id_detalle' => $id_detalle,
                'tokenOperario' => $tokenOperario,
                'id' => $id,
                'idordenproduccion' => $idordenproduccion,
            ]);
        }

        // 2. Preparar el nuevo registro
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $horaCorte = \app\models\HoraCorteEficienciaApp::find()
            ->where(['id_valor' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'fecha_dia' => date('Y-m-d')
            ])    
            ->orderBy(['id_valor' => SORT_DESC])
            ->one();
        
        ///proceso para a hora de inicio.
        $horaActual = new \DateTime();
        // Crea un objeto DateTime a partir de la hora de inicio
        $horaInicio = \DateTime::createFromFormat('H:i:s', $horaCorte->hora_inicio);

        // Compara si la hora actual es mayor que la hora de inicio
      
        if ($horaActual->format('H:i:s') < $horaInicio->format('H:i:s')) {
            Yii::$app->getSession()->setFlash('error', 'El sistema no esta abierto para ingresar operaciones. Hora de inicio de confeccion : ' . $horaCorte->hora_inicio . '. ');
            return $this->redirect([
                'entrada_operacion_talla',
                'id_planta' => $id_planta,
                'id_detalle' => $id_detalle,
                'tokenOperario' => $tokenOperario,
                'id' => $id,
                'idordenproduccion' => $idordenproduccion,
            ]);
        }
        
        // Busca el ultimo registro de ese empleado
        $ultimoRegistro = \app\models\ValorPrendaUnidadDetalles::find()
            ->where([
                'id_operario' => $tokenOperario,
                'dia_pago' => date('Y-m-d')
            ])
            ->orderBy(['consecutivo' => SORT_DESC]) // Corrected column name for ordering
            ->one();
        
        //Valida si la persona esta en su hora de comida que no se ingresen operaciones
        if($ultimoRegistro){
            if(date('H:i:s') < $ultimoRegistro->hora_corte){
                Yii::$app->getSession()->setFlash('error', 'Durante el tiempo de alimento y/o tiempo autorizado no se pueden ingresar operaciones.Favor validar la información. ');
                return $this->redirect([
                    'entrada_operacion_talla',
                    'id_planta' => $id_planta,
                    'id_detalle' => $id_detalle,
                    'tokenOperario' => $tokenOperario,
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                ]); 
            }
        }    
         
        //Inicia proceso de inserccion
        
        $table = new \app\models\ValorPrendaUnidadDetalles();
        $table->id_operario = $tokenOperario;
        $table->idordenproduccion = $idordenproduccion;
        $table->operacion = '1'; // Asumiendo que 1 es un valor fijo
        $table->dia_pago = date('Y-m-d');
        $table->cantidad = 1;
        $table->minuto_prenda = $flujo->minutos;

        // 3. Lógica de cálculo de valor según el tipo de operario
        if ($operario->vinculado == 1) {
            $table->vlr_prenda = round($empresa->vlr_minuto_vinculado * $flujo->minutos);
            $table->vlr_pago = $table->vlr_prenda;

            // Comprobar si es la primera operación del día para este operario
            $operacionesDelDia = \app\models\ValorPrendaUnidadDetalles::find()
                ->where(['dia_pago' => date('Y-m-d'), 'id_operario' => $tokenOperario])
                ->count();
            if ($operacionesDelDia == 0) {
                $total_dia = 0;
                $total_dia = $this->CostoOperarioVinculadoApp($tokenOperario);
                $table->costo_dia_operaria = $total_dia;
                $table->control_fecha = 1;
                $table->hora_descontar = 1;
            }
            $table->observacion = 'Vinculado';
        } else {
            $table->vlr_prenda = round($empresa->vlr_minuto_contrato * $flujo->minutos);
            $table->vlr_pago = $table->vlr_prenda;
            $table->costo_dia_operaria = $table->vlr_prenda;
            $table->observacion = 'Al contrato';
        }

        // 4. Asignar atributos adicionales
        $model = \app\models\ValorPrendaUnidad::findOne($id);
        $valor_minuto =  $flujo->ordenproduccion->cliente->minuto_confeccion;
        if($valor_minuto){
            $table->total_valor_venta = round($valor_minuto * $flujo->minutos); 
        }else{
           $table->total_valor_venta = 0;  
        }
        
        $table->id_valor = $id;
        $table->usuariosistema = Yii::$app->user->identity->username;
        $table->hora_inicio_modulo = $horaCorte->hora_inicio;
        $table->aplica_regla = 1;
        $table->aplica_sabado = $horaCorte->aplica_sabado;
        $table->id_planta = $id_planta;
        $table->id_tipo = $model->idtipo;
        $table->iddetalleorden = $id_detalle;
        $table->idproceso = $id_operacion;
        $table->hora_inicio = $horaCorte->hora_inicio;
        $table->hora_corte = date('H:i:s');
        $dia_numero = date('N', strtotime($table->dia_pago));
        $table->dia_semana = $dia_numero;
        $table->tipo_aplicacion = 1;
        
        if(!$ultimoRegistro){// si es primer registro
           $hora_inicial = $horaCorte->hora_inicio;
           $hora_final = $table->hora_corte; 
        }else{
           $hora_inicial = $ultimoRegistro->hora_corte;
           $hora_final = $table->hora_corte; 
        }
        ///permite calcular el tiempo de demora
        
        $tiempo_inicial = new \DateTimeImmutable($hora_inicial);
        $tiempo_final = new \DateTimeImmutable($hora_final);
       // Calcular la diferencia entre los dos objetos DateTime
        $diferencia = $tiempo_final->diff($tiempo_inicial);

        // Obtener el número total de segundos
        $segundos = ($diferencia->days * 86400) + ($diferencia->h * 3600) + ($diferencia->i * 60) + $diferencia->s;

        // Convertir los segundos a minutos y redondear
        $minutos = round($segundos / 60, 2);
        //acumula los tres eventos
        $total_acumulado_minutos = 0;
        $tiempo_minimo = 0;
        if($empresa->total_eventos > 0){
            $total_acumulado_minutos = $flujo->minutos * $empresa->total_eventos;
        }
        $tiempo_minimo = round($flujo->minutos / 2, 2);
        if($minutos > 0){
           //formula para la eficiencia
            $EficienciaOperacion = round(($flujo->minutos / $minutos)* 100,2); 
            if($EficienciaOperacion > $empresa->tiempo_maximo_operacion){
               if($minutos < $tiempo_minimo){
                   $table->porcentaje_cumplimiento = $empresa->sam_minimo; 
                   //PROCESO QUE GUARDA LA BITACORA
                   $sam = $table->porcentaje_cumplimiento;
                   $variable = 'El operario envia una operacion en un tiempo minimo, desborda la eficiencia';
                   $this->GuardarBitacoraEficiencia($tokenOperario, $idordenproduccion, $id_operacion, $id_detalle, $variable, $minutos, $sam);
               }else{
                   $table->porcentaje_cumplimiento = $empresa->tiempo_maximo_operacion; 
               }    
            }else{
               if($minutos > $total_acumulado_minutos){
                    if($empresa->aplica_regla_castigo == 0){
                        $table->porcentaje_cumplimiento = $EficienciaOperacion;
                        
                    }else{
                        $table->porcentaje_cumplimiento = $empresa->sam_castigo;
                    }
                    $sam = $table->porcentaje_cumplimiento;
                    //PROCESO QUE GUARDA LA BITACORA
                    $variable = 'El operario acumula unidades sin eniviarlas a la APP.';
                    $this->GuardarBitacoraEficiencia($tokenOperario, $idordenproduccion, $id_operacion, $id_detalle, $variable, $minutos, $sam);
                    
               }else{
                    $table->porcentaje_cumplimiento = $EficienciaOperacion;
               }
             
            } //fin si
        }else{
           return $this->redirect([
            'entrada_operacion_talla',
            'id_planta' => $id_planta,
            'id_detalle' => $id_detalle,
            'tokenOperario' => $tokenOperario,
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            ]);
        }
        
        
        $table->tiempo_real_confeccion = $minutos;
        $table->diferencia_tiempo = $flujo->minutos - $minutos;
        
        // 5. Guardar el registro y manejar errores de validación
        if ($table->save()) {
           //guarda la unidad en el flujo de operacion
            $this->ActualizarTallasOperaciones($id_detalle, $idordenproduccion, $flujo, $id_operacion);
            $this->ActualizaSoloOperaciones($id_detalle, $id_operacion, $idordenproduccion);
            //PREGUNA SI LA OPERACION DESCARGAR LAS UNIDADES
            if($flujo->aplica_modulo == 1){
               $operacion = $flujo->idproceso;
               $this->DescargarUnidadeOrdenProduccion($id_detalle, $idordenproduccion, $operacion, $tokenOperario, $id_planta);
            }
            Yii::$app->getSession()->setFlash('success', 'El registro se guardó exitosamente a las : '.$table->hora_corte.'.');
        } else {
            // En caso de error, obtenemos y mostramos los detalles
            $errores = json_encode($table->getErrors());
            Yii::$app->getSession()->setFlash('error', "Error de comunicación, no se guardaron los registros. Detalles: $errores");
        }

        // 6. Redirigir siempre después de intentar guardar
      return $this->redirect([
            'entrada_operacion_talla',
            'id_planta' => $id_planta,
            'id_detalle' => $id_detalle,
            'tokenOperario' => $tokenOperario,
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
        ]);
    }
    
    //PROCESO QUE GUARDA LA BITAGORA
    protected function GuardarBitacoraEficiencia($tokenOperario, $idordenproduccion, $id_operacion, $id_detalle, $variable, $minutos, $sam ) {
        if($tokenOperario && $idordenproduccion && $id_operacion){
            $table = new \app\models\BitacoraEficienciaOperario();
            $table->id_operario = $tokenOperario;
            $table->idordenproduccion = $idordenproduccion;
            $table->idproceso = $id_operacion;
            $table->iddetalleorden = $id_detalle;
            $table->hora_corte = date('H:i:s');
            $table->concepto = $variable;
            $table->fecha_confeccion = date('Y-m-d');
            $table->tiempo_real_confeccion = $minutos;
            $table->porcentaje_eficiencia = $sam;
            $table->save();
        }
    }
    ////PROCESO QUE DESCARGA LAS UNIDADES DE LA OP
    protected function DescargarUnidadeOrdenProduccion($id_detalle, $idordenproduccion, $operacion, $tokenOperario, $id_planta) {
        $total = 0; $dato = 0;
        //deacarga las unidades por tallas
        $detalleOrden = \app\models\Ordenproducciondetalle::findOne($id_detalle);
        $detalleOrden->faltante += 1;
        $detalleOrden->cantidad_operada += 1;
        $total = ($detalleOrden->cantidad_operada / $detalleOrden->cantidad)* 100;
        $detalleOrden->porcentaje_cantidad = $total;
        $detalleOrden->save();
        
        //buscar todas las tallas
        $detalleOrdenTotal = \app\models\Ordenproducciondetalle::find()->where(['idordenproduccion' => $idordenproduccion])->all();
        foreach ($detalleOrdenTotal as $totales) {
            $dato += $totales->cantidad_operada;
        }
        $orden = Ordenproduccion::findOne($idordenproduccion);
        $orden->faltante = $orden->cantidad - $dato;
        $orden->porcentaje_cantidad = ($dato / $orden->cantidad ) * 100;
        $orden->save();
        
        //busca el balanceo
        $balanceoDetalle = \app\models\BalanceoDetalle::find()->where([
                                                'idordenproduccion' => $idordenproduccion,
                                                'id_proceso' => $operacion,
                                                'id_operario' => $tokenOperario])->one();
        
        $balanceo = \app\models\Balanceo::findOne($balanceoDetalle->id_balanceo);
        // graba las unidades en la tabla cantidad prendas terminadas
        $table = new \app\models\CantidadPrendaTerminadas();
        $table->id_balanceo = $balanceo->id_balanceo; // ojo;
        $table->idordenproduccion = $idordenproduccion;
        $table->iddetalleorden = $id_detalle;
        $table->id_proceso_confeccion = $balanceo->id_proceso_confeccion; 
        $table->id_planta = $id_planta;
        $table->cantidad_terminada = 1;
        $table->nro_operarios = $balanceo->cantidad_empleados;
        $table->fecha_entrada = date('Y-m-d');
        $table->fecha_procesada = date('Y-m-d H:i:s');
        $table->hora_corte_entrada = date('H:i:s');
        $table->usuariosistema = Yii::$app->user->identity->username;
        $table->save();                         
    }
    
    //PROCESO QUE ACTUALIZA LA LAS TALLAS DE LA ORDEN DE PRODUCCION Y LAS OPERACIONES
    protected function ActualizarTallasOperaciones($id_detalle, $idordenproduccion, $flujo, $id_operacion) {
        $buscarOperaciones = ValorPrendaUnidadDetalles::find()->where([
                                                    'idordenproduccion' => $idordenproduccion,
                                                    'idproceso' => $id_operacion,
                                                    'iddetalleorden' => $id_detalle
                                               ])->count();
        $flujo->cantidad_confeccionadas = $buscarOperaciones;
        $flujo->save();
        //actualiza la orden produccion
        $buscarTallas = \app\models\ValorPrendaUnidadDetalles::find()->where([
                                                    'idordenproduccion' => $idordenproduccion,
                                                    'iddetalleorden' => $id_detalle
                                               ])->count();
        $detalle = \app\models\Ordenproducciondetalle::findOne($id_detalle);
        $detalle->cantidad_confeccionada = $buscarTallas; 
        $detalle->save();
    }
    
    //PROCESO QUE SOLO ACTUALIZA LA OPERACIONES
    protected function ActualizaSoloOperaciones($id_detalle, $id_operacion, $idordenproduccion) {
        
        
        $buscarOperaciones = ValorPrendaUnidadDetalles::find()->where([
                                                    'idordenproduccion' => $idordenproduccion,
                                                    'idproceso' => $id_operacion,
                                                    'iddetalleorden' => $id_detalle
                                               ])->count();
        //busca la operacion para actualizarla
        $flujo = \app\models\Ordenproducciondetalleproceso::find()->where([
                                                                 'idproceso' => $id_operacion,
                                                                 'iddetalleorden' => $id_detalle])->one();
        $flujo->unidades_confeccionadas = $buscarOperaciones;
        $flujo->save();
    }
    
    //PERMITE INGRESAR EL TIEMPO DEL DESAYUNO
    public function actionCargar_tiempo_desayuno($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle){
        /// 1. prepara el ultimo registro
        $ultimoRegistro = \app\models\ValorPrendaUnidadDetalles::find()
            ->where([
                'id_operario' => $tokenOperario,
                'dia_pago' => date('Y-m-d')
            ])
            ->orderBy(['consecutivo' => SORT_DESC]) // Corrected column name for ordering
            ->one();
        if($ultimoRegistro){
            $horario = \app\models\Horario::findOne(1);
            $ultimaHora = $ultimoRegistro->hora_corte; 
            $tiempo_ultima_hora = new \DateTimeImmutable($ultimaHora);
            $nueva_hora_sumada = $tiempo_ultima_hora->modify('+'.$horario->tiempo_desayuno. ' minutes');
            $ultimoRegistro->hora_inicio_desayuno = date('H:i:s');
            $ultimoRegistro->hora_corte = $nueva_hora_sumada->format('H:i:s');
            $nueva_hora_entrada = $ultimoRegistro->hora_corte;
            if($ultimoRegistro->save()){
                Yii::$app->getSession()->setFlash('success', 'Se activo el horario del desayuno. Cuenta con '.$horario->tiempo_desayuno. ' minutos. Hora de regreso debe de ser a las : ('.$nueva_hora_entrada.').');
                return $this->redirect([
                    'entrada_operacion_talla',
                    'id_planta' => $id_planta,
                    'id_detalle' => $id_detalle,
                    'tokenOperario' => $tokenOperario,
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'nueva_hora_entrada' => $nueva_hora_entrada,
                 ]);
            }
            
        }else{
           Yii::$app->getSession()->setFlash('error', 'No hay registro en la tabla para mostrar. Valide la informacion.'); 
            return $this->redirect([
                 'entrada_operacion_talla',
                 'id_planta' => $id_planta,
                 'id_detalle' => $id_detalle,
                 'tokenOperario' => $tokenOperario,
                 'id' => $id,
                 'idordenproduccion' => $idordenproduccion,
             ]);
        }
        
    }
    
    //PERMITE INGRESAR LA HORA DE ALMUERZO
    public function actionCargar_tiempo_almuerzo($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle){
        /// 1. prepara el ultimo registro
        $ultimoRegistro = \app\models\ValorPrendaUnidadDetalles::find()
            ->where([
                'id_operario' => $tokenOperario,
                'dia_pago' => date('Y-m-d')
            ])
            ->orderBy(['consecutivo' => SORT_DESC]) 
            ->one();
        if($ultimoRegistro){
            $horario = \app\models\Horario::findOne(1);
            $ultimaHora = $ultimoRegistro->hora_corte; 
            $tiempo_ultima_hora = new \DateTimeImmutable($ultimaHora);
            $nueva_hora_sumada = $tiempo_ultima_hora->modify('+'.$horario->tiempo_almuerzo. ' minutes');
            $ultimoRegistro->hora_inicio_almuerzo = $ultimoRegistro->hora_corte;
            $ultimoRegistro->hora_corte = $nueva_hora_sumada->format('H:i:s');
            $nueva_hora_entrada = $ultimoRegistro->hora_corte;
            if($ultimoRegistro->save()){
                Yii::$app->getSession()->setFlash('success', 'Se activo el horario del almuerzo. Cuenta con '.$horario->tiempo_desayuno. ' minutos. La Hora de regreso debe de ser a las : ('.$nueva_hora_entrada.').');
                return $this->redirect([
                    'entrada_operacion_talla',
                    'id_planta' => $id_planta,
                    'id_detalle' => $id_detalle,
                    'tokenOperario' => $tokenOperario,
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'nueva_hora_entrada' => $nueva_hora_entrada,
                 ]);
            }
            
        }else{
           Yii::$app->getSession()->setFlash('error', 'No hay registro en la tabla para mostrar. Valide la informacion.'); 
            return $this->redirect([
                 'entrada_operacion_talla',
                 'id_planta' => $id_planta,
                 'id_detalle' => $id_detalle,
                 'tokenOperario' => $tokenOperario,
                 'id' => $id,
                 'idordenproduccion' => $idordenproduccion,
             ]);
        }
        
    }
    
    
    //PERMITE INGRESAR LA HORA DE ALMUERZO
    public function actionValidar_tiempo_desuso($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle){
        /// 1. prepara el ultimo registro
        $ultimoRegistro = \app\models\ValorPrendaUnidadDetalles::find()
            ->where([
                'id_operario' => $tokenOperario,
                'dia_pago' => date('Y-m-d')
            ])
            ->orderBy(['consecutivo' => SORT_DESC]) 
            ->one();
        if($ultimoRegistro){
            $horario = \app\models\Horario::findOne(1);
            $tiempo_pausa_operario_minutos = $horario->minutos_desuso; // Esto debe ser 1.5

            // 1. Convierte los minutos a segundos
            // Se usa (float) para asegurar que el valor sea decimal si no lo es.
            $tiempo_pausa_segundos = floor((float)$tiempo_pausa_operario_minutos * 60);

            $ultimaHora = $ultimoRegistro->hora_corte; 
            $tiempo_ultima_hora = new \DateTimeImmutable($ultimaHora);

            // 2. Suma el tiempo en segundos
            $nueva_hora_sumada = $tiempo_ultima_hora->modify('+'.$tiempo_pausa_segundos. ' seconds'); 

            $ultimoRegistro->hora_inicio_desuso = $ultimoRegistro->hora_corte;
            $ultimoRegistro->hora_corte = $nueva_hora_sumada->format('H:i:s');
            $nueva_hora_entrada = $ultimoRegistro->hora_corte;
            $ultimoRegistro->tiempo_desuso = 1;
            if($ultimoRegistro->save()){
                Yii::$app->getSession()->setFlash('warning', 'Se activo el horario para ir al baño o descanso. Cuenta con '.$horario->minutos_desuso. ' minutos. La Hora de regreso debe de ser a las : ('.$nueva_hora_entrada.').');
                return $this->redirect([
                    'entrada_operacion_talla',
                    'id_planta' => $id_planta,
                    'id_detalle' => $id_detalle,
                    'tokenOperario' => $tokenOperario,
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'nueva_hora_entrada' => $nueva_hora_entrada,
                 ]);
            }
            
        }else{
           Yii::$app->getSession()->setFlash('error', 'No hay registro en la tabla para mostrar. Valide la informacion.'); 
            return $this->redirect([
                 'entrada_operacion_talla',
                 'id_planta' => $id_planta,
                 'id_detalle' => $id_detalle,
                 'tokenOperario' => $tokenOperario,
                 'id' => $id,
                 'idordenproduccion' => $idordenproduccion,
             ]);
        }
        
    }
    
    //PERMITE VALIDAR EL SAM PARA LA INDUCCION DE LA OPERACION
    //PERMITE INGRESAR LA HORA DE ALMUERZO
    public function actionSam_induccion_operacion($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle, $id_operacion){
        /// 1. prepara el ultimo registro
        $ultimoRegistro = \app\models\ValorPrendaUnidadDetalles::find()
            ->where([
                'id_operario' => $tokenOperario,
                'dia_pago' => date('Y-m-d')
            ])
            ->orderBy(['consecutivo' => SORT_DESC]) 
            ->one();
        if($ultimoRegistro){
            $horario = \app\models\FlujoOperaciones::find()->where([
                                        'idproceso' => $id_operacion,
                                        'idordenproduccion' => $idordenproduccion])->one();
            $ultimaHora = $ultimoRegistro->hora_corte; 
            $tiempo_ultima_hora = new \DateTimeImmutable($ultimaHora);
            $nueva_hora_sumada = $tiempo_ultima_hora->modify('+'.$horario->tiempo_induccion. ' minutes');
            $ultimoRegistro->hora_inicio_induccion = $ultimoRegistro->hora_corte;
            $ultimoRegistro->hora_corte = $nueva_hora_sumada->format('H:i:s');
            $nueva_hora_entrada = $ultimoRegistro->hora_corte;
            $ultimoRegistro->tiempo_induccion = $horario->tiempo_induccion ;
            //actualizamos el registro
            $horario->aplica_induccion = 1;
            $horario->save();
            if($ultimoRegistro->save()){
                Yii::$app->getSession()->setFlash('warning', 'Se activo el horario para la induccion. Cuenta con '.$horario->tiempo_induccion. ' minutos. La Hora de regreso: ('.$ultimoRegistro->hora_corte.').');
                return $this->redirect(['entrada_operacion_talla',
                    'id_planta' => $id_planta,
                    'id_detalle' => $id_detalle,
                    'tokenOperario' => $tokenOperario,
                    'id' => $id,
                    'idordenproduccion' => $idordenproduccion,
                    'nueva_hora_entrada' => $nueva_hora_entrada,
                 ]);
            }
            
        }else{
           Yii::$app->getSession()->setFlash('error', 'No hay registro en la tabla para mostrar. Valide la informacion.'); 
            return $this->redirect(['entrada_operacion_talla',
                 'id_planta' => $id_planta,
                 'id_detalle' => $id_detalle,
                 'tokenOperario' => $tokenOperario,
                 'id' => $id,
                 'idordenproduccion' => $idordenproduccion,
             ]);
        }
        
    }
    
    //PERMITE MOSTRAR LAS TALAS DE QUE TIENE CADA ORDEN DE PRODUCCION
    public function actionEntrada_operacion_talla($id, $idordenproduccion, $id_planta, $tokenOperario, $id_detalle) {
        
        $hora_corte_app = \app\models\HoraCorteEficienciaApp::find()->where([
                                                                    'idordenproduccion' => $idordenproduccion,
                                                                    'id_valor' => $id,
                                                                    'fecha_dia' => date('Y-m-d')
                                                                    ])->one();
        $horaActual = new \DateTime();
        $horaCierre = \DateTime::createFromFormat('H:i:s', $hora_corte_app->hora_cierre);
        if($horaCierre == null){
            $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_operario', $tokenOperario])
                                                                                ->andWhere(['=','idordenproduccion', $idordenproduccion])
                                                                                ->andWhere(['=','estado_operacion', 0])->all();
           //Permite calcular la eficiencia
            $vector_eficiencia = ValorPrendaUnidadDetalles::find()
                    ->where([
                            'id_operario' => $tokenOperario,
                            'dia_pago' => date('Y-m-d')
                            ])->all();

            $tallas = \app\models\Ordenproducciondetalle::findOne($id_detalle);
            if($tallas->cantidad_operaciones == $tallas->cantidad_confeccionada){
                Yii::$app->getSession()->setFlash('error', 'No se puede ingresar mas operaciones en esta talla. Valide la información.'); 
                return $this->redirect(['view_produccion',
                     'id_planta' => $id_planta,
                     'tokenOperario' => $tokenOperario,
                     'id' => $id,
                     'idordenproduccion' => $idordenproduccion,
                 ]);
            }
            $nueva_hora_entrada = '';
            return $this->render('entrada_operacion_talla', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'detalle_balanceo' =>  $detalle_balanceo,
                'id_detalle' => $id_detalle,
                'tokenOperario' => $tokenOperario,
                'idordenproduccion' => $idordenproduccion,
                'tallas' => $tallas,
                'nueva_hora_entrada' => $nueva_hora_entrada,
                'vector_eficiencia' => $vector_eficiencia,
            ]);
        }
        
        if($horaActual > $horaCierre){
            Yii::$app->getSession()->setFlash('error', 'Esta OP se encuentra cerrada por el dia de hoy. Valide la informacion con el administrador.'); 
               return $this->redirect(['view_produccion',
                     'id_planta' => $id_planta,
                     'tokenOperario' => $tokenOperario,
                     'id' => $id,
                     'idordenproduccion' => $idordenproduccion,
                 ]); 
        }else{
            $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_operario', $tokenOperario])
                                                                                ->andWhere(['=','idordenproduccion', $idordenproduccion])
                                                                                ->andWhere(['=','estado_operacion', 0])->all();
           //Permite calcular la eficiencia
            $vector_eficiencia = ValorPrendaUnidadDetalles::find()
                    ->where([
                            'id_operario' => $tokenOperario,
                            'dia_pago' => date('Y-m-d')
                            ])->all();

            $tallas = \app\models\Ordenproducciondetalle::findOne($id_detalle);
            if($tallas->cantidad_operaciones == $tallas->cantidad_confeccionada){
                Yii::$app->getSession()->setFlash('error', 'No se puede ingresar mas operaciones en esta talla. Valide la información.'); 
                return $this->redirect(['view_produccion',
                     'id_planta' => $id_planta,
                     'tokenOperario' => $tokenOperario,
                     'id' => $id,
                     'idordenproduccion' => $idordenproduccion,
                 ]);
            }
            $nueva_hora_entrada = '';
            return $this->render('entrada_operacion_talla', [
                'model' => $this->findModel($id),
                'id_planta' =>$id_planta,
                'detalle_balanceo' =>  $detalle_balanceo,
                'id_detalle' => $id_detalle,
                'tokenOperario' => $tokenOperario,
                'idordenproduccion' => $idordenproduccion,
                'tallas' => $tallas,
                'nueva_hora_entrada' => $nueva_hora_entrada,
                'vector_eficiencia' => $vector_eficiencia,
            ]);
        }    
    }
    
    protected function CostoOperarioVinculadoApp($tokenOperario) {
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $valorCesantia = 0; 
        $valorPrima = 0;
        $vlrDia = 0;
        $valorInteres = 0;
        $totalDia = 0;
        $valorVacacion = 0; 
        $valorArl = 0;
        $auxilioT = 0;
        $valor_eps = 0;
        $valor_pension = 0;
        $valor_ajuste = 0;
        $valor_caja = 0;
        //modelos
        $parametros = \app\models\Parametros::findOne(1);
        $operario = Operarios::findOne($tokenOperario);
        $vlrDia = round($operario->salario_base / $empresa->dias_trabajados);
        $auxilio = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
        $auxilioT = round($auxilio->auxilio_transporte_actual / $empresa->dias_trabajados);
        //prestaciones
        $valorPrima = round($vlrDia * $empresa->porcentaje_prima)/100;
        $valorCesantia = round($vlrDia * $empresa->porcentaje_cesantias)/100;
        $valorInteres = round($vlrDia * $empresa->porcentaje_intereses)/100;
        $valorVacacion = round($vlrDia * $empresa->porcentaje_vacacion)/100;
        $valor_ajuste = round($valorVacacion * $empresa->ajuste_caja)/100;
        //seguridad social
        $valor_caja = round($vlrDia * $parametros->caja)/100;
        $valor_eps = round($vlrDia * $parametros->eps)/100;
        $valor_pension = round($vlrDia * $parametros->pension)/100;
        $valorArl = round($vlrDia * $operario->arl->arl)/100;
        //totales
        $total_dia = round($valorPrima + $valorCesantia + $valorInteres + $valorVacacion + $valor_ajuste + $valorArl + $valor_eps + $valor_pension + $valor_caja + $vlrDia + $auxilioT);
        return ($total_dia);
    
    }
    
    
    //PROCESO QUE CANCULA LA EFICIENCIA DEL OPERARIO
    protected function CalcularEficienciaOperario($operario, $idordenproduccion, $id, $id_detalle) {
        $table = ValorPrendaUnidadDetalles::find()->orderBy('consecutivo DESC')->one();
        $operarios = Operarios::findOne($operario);
        $conMatricula = \app\models\Matriculaempresa::findOne(1);
        $auxiliar = $table->control_fecha;
        $totalTiempo = 0; $total_minutos = 0; $Talimento = 0;
        $total_diario = 0; $sw = 0; $sumarh = 0; $sumarm = 0; $can_minutos = 0; $metaDiaria = 0; $cumplimiento = 0;
        if($operarios->vinculado == 1){ //costo personal vinculado
            $this->CostoOperarioVinculado($table, $auxiliar);
            $inicio  = explode(":", $table->hora_inicio);
            $minutos_inicio = ($inicio[0] * 60) + $inicio[1];
            $corte = explode(":", $table->hora_corte);
            $minutos_corte = ($corte[0] * 60) + $corte[1];
            $total_minutos = $minutos_corte - $minutos_inicio;
            if($table->alimentacion == 0){
                $Talimento = 0;
            }else{
                if($table->hora_corte <= '10:00'){
                    $Talimento = $operarios->horarios->tiempo_desayuno; //son minutos
                }else{
                    $Talimento = $operarios->horarios->tiempo_almuerzo; //son minutos
                }               
            }    
           
            $totalTiempo = ($total_minutos - $Talimento); // si tiene desayuno totaliza el tiempo total
           //busca valor del minuto vinculado
            $total_tiempo_operacion = $table->vlr_prenda / $conMatricula->vlr_minuto_vinculado;
            $total_meta_corte = round((60 / $total_tiempo_operacion) * ($totalTiempo / 60), 0); //saca la meta por corte al 100%
            $cumplimiento = round(($table->cantidad / $total_meta_corte) * 100, 2); //genera el cumplimiento por corte
            $metaDiaria = $total_meta_corte;
            //calcula 
            $table->porcentaje_cumplimiento = $cumplimiento;
            $table->meta_diaria = $metaDiaria;
            $table->save(false);
        }
        else{ // personal no vinculado
            $this->CostoOperarioVinculado($table, $auxiliar);
            $inicio  = explode(":", $table->hora_inicio);
            $minutos_inicio = ($inicio[0] * 60) + $inicio[1];
            $corte = explode(":", $table->hora_corte);
            $minutos_corte = ($corte[0] * 60) + $corte[1];
            $total_minutos = $minutos_corte - $minutos_inicio;
            if($table->alimentacion == 0){
                $Talimento = 0;
            }else{
                if($table->hora_corte <= '10:00'){
                    $Talimento = $operarios->horarios->tiempo_desayuno; //son minutos
                }else{
                    $Talimento = $operarios->horarios->tiempo_almuerzo; //son minutos
                }               
            }    
            $totalTiempo = ($total_minutos - $Talimento); // si tiene desayuno totaliza el tiempo total
           //busca valor del minuto vinculado
            $total_tiempo_operacion = $table->vlr_prenda / $conMatricula->vlr_minuto_contrato;
            $total_meta_corte = round((60 / $total_tiempo_operacion) * ($totalTiempo / 60), 0); //saca la meta por corte al 100%
            $cumplimiento = round(($table->cantidad / $total_meta_corte) * 100, 2); //genera el cumplimiento por corte
            $metaDiaria = $total_meta_corte;
            //calcula 
            $table->porcentaje_cumplimiento = $cumplimiento;
            $table->meta_diaria = $metaDiaria;
            $table->save(false);
        }
    }
    
    ///PROCESO QUE CREA LA HORA DE INICIO O CORTE
    public function actionCrear_hora_corte($id, $tokenPlanta, $tipo_pago, $id_planta, $idordenproduccion) {

        $model = new \app\models\FormCostoGastoEmpresa();
        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST["generar_hora_corte"])) {
                $fechaDia = date('Y-m-d');
                $orden = Ordenproduccion::findOne($idordenproduccion);
                $buscar = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $id])->andWhere(['=','hora_inicio', $model->hora_inicio])
                                                                        ->andWhere(['=','hora_corte', $model->hora_corte])->andWhere(['=','idordenproduccion', $idordenproduccion])
                                                                        ->andWhere(['=','fecha_proceso', $fechaDia])->one();
                if(!$buscar){
                    $table = new \app\models\ValorPrendaCorteConfeccion();
                    $table->id_valor = $id;
                    $table->idordenproduccion = $idordenproduccion;
                    $table->codigo_producto = $orden->codigoproducto;
                    $table->hora_inicio = $model->hora_inicio;
                    $table->hora_corte = $model->hora_corte;
                    $table->fecha_proceso = $fechaDia;
                    $table->user_name = Yii::$app->user->identity->username;
                    $table->save(false);
                    return $this->redirect(['valor-prenda-unidad/search_tallas_ordenes','id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' =>$id, 'tokenPlanta' => $tokenPlanta,'tipo_pago' => $tipo_pago]);
                }else{
                    Yii::$app->getSession()->setFlash('error', 'La hora de corte para el ingreso de operaciones ya existe para esta OP. Vallidar la informacion.');
                    return $this->redirect(['valor-prenda-unidad/search_tallas_ordenes','id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' =>$id, 'tokenPlanta' => $tokenPlanta,'tipo_pago' => $tipo_pago]);
                }
            }
        }
        return $this->renderAjax('crear_hora_corte', [
            'model' => $model,       
        ]);    
    }
    
    ///PROCESO QUE CREA LA HORA DE INICIO O CORTE APP
    public function actionCrear_hora_corte_app($id, $tokenPlanta, $tipo_pago, $id_planta, $idordenproduccion) {

        $model = new \app\models\HoraCorteEficienciaApp();
      
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
             // Busca si ya existe una hora de corte para evitar duplicados.
               $fechaBusqueda = date('Y-m-d', strtotime($model->fecha_dia));
                $existeCorte = \app\models\HoraCorteEficienciaApp::find()
                        ->where([
                            'id_valor'          => $id,
                            'idordenproduccion' => $idordenproduccion,
                            'fecha_dia'         => $fechaBusqueda,
                        ])->one();

                if ($existeCorte) {
                    // Si la hora de corte ya existe, muestra un mensaje de error y redirige.
                    Yii::$app->getSession()->setFlash('error', 'La hora de corte para el ingreso de operaciones ya existe para esta OP. Validar la información.');
                   
                } else {
                    // Si no existe, crea un nuevo registro.
                    $orden = \app\models\Ordenproduccion::findOne($idordenproduccion);
                    // Asigna los valores adicionales
                    $model->id_valor = $id;
                    $model->idordenproduccion = $idordenproduccion;
                    $model->codigo_producto = $orden ? $orden->codigoproducto : null;
                    $model->fecha_registro = date('Y-m-d H:i:s');
                    $model->user_name = Yii::$app->user->identity->username;
                    $model->aplica_sabado = $model->aplica_sabado;
                    if ($model->save()) { // Guarda el nuevo registro
                        return $this->redirect([
                            'valor-prenda-unidad/search_tallas_ordenes',
                            'id_planta' => $id_planta,
                            'idordenproduccion' => $idordenproduccion,
                            'id' => $id,
                            'tokenPlanta' => $tokenPlanta,
                            'tipo_pago' => $tipo_pago
                        ]);
                    } else {
                        // Capturamos los errores de validación del modelo si el save falla
                        $errores = implode('<br>', \yii\helpers\ArrayHelper::getColumn($model->getErrors(), 0));
                        Yii::$app->getSession()->setFlash('error', 'Error al guardar: ' . $errores);
                    }
                }
                return $this->redirect([
                    'valor-prenda-unidad/search_tallas_ordenes',
                    'id_planta' => $id_planta,
                    'idordenproduccion' => $idordenproduccion,
                    'id' => $id,
                    'tokenPlanta' => $tokenPlanta,
                    'tipo_pago' => $tipo_pago
                ]);
            
        }
        return $this->renderAjax('crear_hora_corte_app', [
            'model' => $model,       
        ]);    
    }
    
    //EDITAR LINEA DE CONFECCION
    public function actionEditar_linea_confeccion($id_detalle) {
        $model = new \app\models\FormFiltroControlLinea();
        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST["actualizar_linea"])) {
                $table = ValorPrendaUnidadDetalles::findOne($id_detalle);
                if($model->nueva_fecha <> null && $model->nueva_linea <> null){
                    $table->dia_pago = $model->nueva_fecha;
                    $table->hora_descontar = $model->nueva_linea;
                    $table->save(false);
                }else{
                     if($model->nueva_fecha <> null){
                        $table->dia_pago = $model->nueva_fecha;
                        $table->save(false);
                     }else{
                        if($model->nueva_linea <> null){
                            $table->hora_descontar = $model->nueva_linea;
                            $table->save(false); 
                        } else{
                            Yii::$app->getSession()->setFlash('warning', 'Debe de seleccionar al menos una opcion. Vallidar la informacion.'); 
                        }   
                     }
                }
                return $this->redirect(['valor-prenda-unidad/control_linea_confeccion']);
            }
            
        }
         return $this->renderAjax('_editar_linea_confeccion', [
            'model' => $model,       
        ]);    
    }
    
    ///PROCESO QUE CREA LA HORA DE INICIO O CORTE
    public function actionEditar_hora_corte($id, $tokenPlanta, $tipo_pago, $id_planta, $idordenproduccion) {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 153])->all()) {
                $model = new \app\models\FormCostoGastoEmpresa();
                if ($model->load(Yii::$app->request->post())) {
                    if (isset($_POST["editar_hora_corte"])) {
                        $table = \app\models\ValorPrendaCorteConfeccion::findOne($model->codigo);
                        $table->hora_inicio = $model->hora_inicio;
                        $table->hora_corte = $model->hora_corte;
                        $table->fecha_proceso = $model->fecha_proceso;
                        $table->save(false);
                        return $this->redirect(['valor-prenda-unidad/search_tallas_ordenes','id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' =>$id, 'tokenPlanta' => $tokenPlanta,'tipo_pago' => $tipo_pago]);

                    }
                }
                if (Yii::$app->request->get("id")){
                    $table = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $id])->orderBy('id_corte DESC')->one();
                    $model->hora_inicio = $table->hora_inicio;
                    $model->hora_corte = $table->hora_corte;
                    $model->fecha_proceso = $table->fecha_proceso;
                    $model->codigo = $table->id_corte;
                }

                return $this->renderAjax('editar_hora_corte', [
                    'model' => $model,       
                ]); 
            }else{
                return $this->redirect(['site/sinpermiso']); 
            }  
        }else{
           return $this->redirect(['site/login']); 
        }    
    }
    
    ///PROCESO QUE EDITA LA HORA Y LA FECHA PARA LA APP
    public function actionEditar_hora_corte_app($id, $tokenPlanta, $tipo_pago, $id_planta, $idordenproduccion) {
        // 1. Verificación de permisos y autenticación
        if (!Yii::$app->user->identity || !UsuarioDetalle::find()->where(['codusuario' => Yii::$app->user->identity->codusuario, 'id_permiso' => 166])->exists()) {
            return $this->redirect(['site/sinpermiso']);
        }
        
        $model = \app\models\HoraCorteEficienciaApp::find()->where(['id_valor' => $id])->orderBy('id_corte DESC')->one();
        
        if (!$model) {
            Yii::$app->getSession()->setFlash('error', 'El registro que intenta editar no existe.');
            return $this->redirect(['valor-prenda-unidad/search_tallas_ordenes', 'id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' => $id, 'tokenPlanta' => $tokenPlanta, 'tipo_pago' => $tipo_pago]);
        }
         if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            // Validación y guardado
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'El registro se ha editado correctamente.');
                return $this->redirect(['valor-prenda-unidad/search_tallas_ordenes', 'id_planta' => $id_planta, 'idordenproduccion' => $idordenproduccion, 'id' => $id, 'tokenPlanta' => $tokenPlanta, 'tipo_pago' => $tipo_pago]);
            } else {
                // Si el guardado falla, los errores se manejan automáticamente por la vista
                Yii::$app->getSession()->setFlash('error', 'Ocurrió un error al guardar la información. Por favor, inténtelo de nuevo.');
            }
        }

        // 4. Renderizar la vista
        return $this->renderAjax('editar_hora_corte_app', [
            'model' => $model,
        ]);
    }
    
    
    //CREAR HORA DE INICIO Y DE CORTE MASIVO
    public function actionHora_corte_masivo() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 146])->all()) {
                $model = ValorPrendaUnidad::find()->Where(['=', 'cerrar_pago', 0])->orderBy('id_valor DESC')->all();
                if(isset($_POST["enviar_masivo"])){
                    if(isset($_POST['listado_pago'])){
                        $intIndice = 0;
                        $fechaActual = date('Y-m-d');
                        foreach ($_POST["listado_pago"] as $intCodigo):
                            $hora_inicio = $_POST["hora_inicio"][$intIndice];
                            $hora_corte = $_POST["hora_corte"][$intIndice];
                            if($hora_inicio <> '' && $hora_corte <> ''){
                                $buscar = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $intCodigo])->andWhere(['=','hora_inicio', $hora_inicio])
                                                                            ->andWhere(['=','hora_corte', $hora_corte])
                                                                            ->andWhere(['=','fecha_proceso', $fechaActual])->one();
                                if(!$buscar){
                                    $Valor = ValorPrendaUnidad::findOne($intCodigo);
                                    $table = new \app\models\ValorPrendaCorteConfeccion();
                                    $table->id_valor = $intCodigo;
                                    $table->idordenproduccion = $Valor->idordenproduccion;
                                    $table->codigo_producto = $Valor->ordenproduccion->codigoproducto;
                                    $table->hora_inicio = $hora_inicio;
                                    $table->hora_corte = $hora_corte;
                                    $table->fecha_proceso = $fechaActual;
                                    $table->user_name = Yii::$app->user->identity->username;
                                    $table->save(false);
                                    $intIndice++;
                                }else{
                                    Yii::$app->getSession()->setFlash('error', 'La hora de INICIO Y DE CORTE ya esta creada para este costo de operacion. Vallidar la informacion.');
                                    return $this->redirect(['valor-prenda-unidad/hora_corte_masivo']);
                                }
                            }else{
                                $intIndice++;
                            }    
                        endforeach; 
                        return $this->redirect(['valor-prenda-unidad/hora_corte_masivo']);
                    }
                }
                return $this->render('masivo_corte_hora', [
                     'model' => $model,       
                 ]);  
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }    
    }
    
    //PERMITE MOSTRAR LAS HORAS DE CORTE QUE SE HAN GENERADO
    public function actionVer_corte_hora($id_valor) {
        $fechaActual = date('Y-m-d');
        $model = \app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $id_valor])->andWhere(['=','fecha_proceso', $fechaActual])->orderBy('id_corte DESC')->all();
        return $this->renderAjax('search_ver_hora_corte', [
            'model' => $model,       
        ]);
    }
   
   //VISTA DE BUSQUEDA DE OPERACION POR TALLAS
    public function actionView_operacion_talla($id, $token) {
        $detalle_orden = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $id])->all();
        return $this->render('view_operacion_tallas', [
                'detalle_orden' =>$detalle_orden,
                'id' => $id,
                'token' => $token,
            ]);
    }
   
   //SUMAR UNIDADES CONFECCIONADAS Y VALOR COSTO DE CONFECCION
   protected function SumarCantidadCostoConfeccion($id, $id_detalle, $idordenproduccion) {
       $detalle_global = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $idordenproduccion])->all();
       $detalle_talla = ValorPrendaUnidadDetalles::find()->where(['=','iddetalleorden', $id_detalle])->all();
       $pago_prenda = ValorPrendaUnidad::findOne($id);
       $detalle_orden = \app\models\Ordenproducciondetalle::findOne($id_detalle);
       $cantidad = 0; $valor = 0;
      //graba global
        foreach ($detalle_global as $detalles):
           $cantidad += $detalles->cantidad;
           $valor += $detalles->costo_dia_operaria;
        endforeach;
        $pago_prenda->cantidad_operacion = $cantidad;
        $pago_prenda->total_confeccion = $valor;
        $pago_prenda->total_pagar = $valor;
        $pago_prenda->save();
        //guarda la cantidad confeccionada
        $cantidad = 0; $valor = 0; 
        foreach ($detalle_talla as $talla):
            $cantidad += $talla->cantidad;
            $valor += $talla->costo_dia_operaria;
        endforeach;
        $detalle_orden->cantidad_confeccionada = $cantidad;
        $detalle_orden->costo_confeccion = $valor;
        $detalle_orden->save();
   }   
    
    
    
    //PROCESO QUE BUSCA LAS TALLAS DE LA OP.
    
    public function actionSearch_tallas_ordenes($idordenproduccion, $id, $id_planta, $tokenPlanta, $tipo_pago) {
        $conTallas = \app\models\Ordenproducciondetalle::find()->where(['=','idordenproduccion', $idordenproduccion])
                                                               ->andWhere(['=','id_planta', $id_planta])->all();
        $orden = Ordenproduccion::findOne($idordenproduccion);
        return $this->render('search_tallas_ordenes', [
            'model' => $this->findModel($id),
            'id_planta' =>$id_planta,
            'conTallas' =>  $conTallas,
            'orden' => $orden,
            'tipo_pago' => $tipo_pago,
            'tokenPlanta' => $tokenPlanta,
           
        ]);
    }
    // PROCESO QUE BUSCA EN COSTO DEL PERSONAL VINCULADO
  
    protected function CostoOperarioVinculado($table, $auxiliar)
        {
            $empresa = \app\models\Matriculaempresa::findOne(1);
            $valorCesantia = 0; $valorPrima = 0; $vlrDia = 0; $valorInteres = 0;
            $totalDia = 0; $valorVacacion = 0; $valorArl = 0; $auxilioT = 0;
            $ajuste_caja = 0;
            $valor_eps = 0;
            $valor_pension = 0;
            $caja = 0;
            //modelos
            $parametros = \app\models\Parametros::findOne(1);
            $operario =  Operarios::findOne($table->id_operario);
            $vlrDia = round($operario->salario_base / $empresa->dias_trabajados);
            $porcentaje = \app\models\Matriculaempresa::findOne(1);
            $auxilio = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
            $auxilioT = round($auxilio->auxilio_transporte_actual / $empresa->dias_trabajados);
            //prestaciones
            $valorPrima = round($vlrDia * $porcentaje->porcentaje_prima)/100;
            $valorCesantia = round($vlrDia * $porcentaje->porcentaje_cesantias)/100;
            $valorInteres = round($vlrDia * $porcentaje->porcentaje_intereses)/100;
            $valorVacacion = round($vlrDia * $porcentaje->porcentaje_vacacion)/100;
            $ajuste_caja = round($valorVacacion * $porcentaje->ajuste_caja)/100;
            
            //seguridad social
            $caja = round($vlrDia * $parametros->caja)/100;
            $valor_eps = round($vlrDia * $parametros->eps)/100;
            $valor_pension = round($vlrDia * $parametros->pension)/100;
            $valorArl = round($vlrDia * $operario->arl->arl)/100;
            //totales
            $totalDia = $valorPrima + $valorCesantia + $valorInteres + $valorVacacion + $ajuste_caja + $valorArl + $valor_eps + $valor_pension + $caja + $vlrDia + $auxilioT;
            if ($auxiliar == 1){
                $table->costo_dia_operaria = 0;
            }else{
               $table->costo_dia_operaria = $totalDia; 
            }
            $table->save(false);
    }
    
    /**
     * Creates a new ValorPrendaUnidad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ValorPrendaUnidad();
        $orden = Ordenproduccion::find()->where(['=','pagada', 0])->orderBy('idordenproduccion desc')->all();  
        if ($model->load(Yii::$app->request->post()) && $model->save()){
            $model->usuariosistema = Yii::$app->user->identity->username;
            $model->estado_valor = 0; 
            $ordenproduccion = Ordenproduccion::findOne($model->idordenproduccion);
            $model->cantidad = $ordenproduccion->cantidad;
            $model->tipo_proceso_pago = $model->tipo_proceso_pago;
            $model->update();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenValorPrenda"),
        ]);
    }

    /**
     * Updates an existing ValorPrendaUnidad model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $orden = Ordenproduccion::find()->where(['=','pagada', 0])->orderBy('idordenproduccion desc')->all();  
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $ordenproduccion = Ordenproduccion::findOne($model->idordenproduccion);
            $model->usuario_editado = Yii::$app->user->identity->username;
            $fecha = date('Y-m-d h:i:s');
            $model->fecha_editado = $fecha;
            $model->cantidad = $ordenproduccion->cantidad;
             $model->tipo_proceso_pago = $model->tipo_proceso_pago;
            $model->update();
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
            'orden' => ArrayHelper::map($orden, "idordenproduccion", "ordenValorPrenda"),
        ]);
    }

    /**
     * Deletes an existing ValorPrendaUnidad model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
            $this->redirect(["valor-prenda-unidad/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["valor-prenda-unidad/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            $this->redirect(["valor-prenda-unidad/index"]);
        }
    }

    protected function findModel($id)
    {
        if (($model = ValorPrendaUnidad::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
   
    //MODAL QUE BUSCA LAS OPERACIONES DE LOS OPERARIOS EN PREPARACION
    public function actionBuscaroperaciones($id, $idordenproduccion, $id_planta, $id_tipo) {
        
        if (Yii::$app->request->post()) {
            if (isset($_POST["validaroperario"])) {
                if (isset($_POST["id_detalle"])) {
                    $intIndice = 0;
                    $empresa = \app\models\Matriculaempresa::find()->where(['=','id', 1])->one();
                    foreach ($_POST["id_detalle"] as $intCodigo):
                        $detalle_balanceo = \app\models\BalanceoDetalle::findOne($intCodigo);
                        $operarios = Operarios::find()->where(['=','id_operario', $detalle_balanceo->id_operario])->one();
                        $valor = 0;
                        if ($operarios->vinculado == 1){
                           $valor = number_format($detalle_balanceo->minutos * $empresa->vlr_minuto_vinculado, 0);
                           $vinculado = 'Vinculado';
                        }else{
                            $valor = number_format($detalle_balanceo->minutos * $empresa->vlr_minuto_contrato, 0);
                            $vinculado = 'No vinculado';
                        }
                        $prenda = new ValorPrendaUnidadDetalles();
                        $prenda->id_operario = $detalle_balanceo->id_operario;
                        $prenda->id_valor = $id;                
                        $prenda->idordenproduccion = $idordenproduccion;
                        $prenda->dia_pago= date('Y-m-d');
                        $prenda->operacion = 2;
                        $prenda->vlr_prenda = $valor;
                        $prenda->id_planta = $id_planta;
                         $prenda->id_tipo = $id_tipo  ;
                        $prenda->observacion = $vinculado;
                        $prenda->save(false);
                        $intIndice ++;
                    endforeach;
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
                }
            }
        }   
        return $this->renderAjax('_buscaroperacionesmodulo', [
          //  'model' => $model,
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            'id_planta' => $id_planta,
            'id_tipo' => $id_tipo,
            ]);
    }
        
        
    //PROCESOS Y SUBPROCESOS
    
     public function actionNuevodetalle($id,$idordenproduccion, $id_planta, $tipo_pago)
    {              
        $valor_unidad = ValorPrendaUnidad::findOne($id);
        if($tipo_pago == 1){
            if($valor_unidad->cantidad_operacion > $valor_unidad->cantidad){
               $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta]); 
               Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de operaciones  '.$valor_unidad->cantidad_operacion.' es mayor que la cantidad del lote '.$valor_unidad->cantidad .'.');  
            }else{
                if($valor_unidad->cantidad_procesada > $valor_unidad->cantidad){
                    $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]); 
                    Yii::$app->getSession()->setFlash('error', 'No se puede generar mas lineas porque la cantidad de confeccion y/o Terminación '.$valor_unidad->cantidad_procesada.' es mayor o igual que la cantidad del lote '.$valor_unidad->cantidad.'.');
                }else{    
                    $model = new ValorPrendaUnidadDetalles();
                    $model->id_valor = $id;                
                    $model->idordenproduccion = $idordenproduccion;
                    $model->dia_pago= date('Y-m-d');
                    $model->id_planta = $id_planta;
                    $model->id_tipo = $valor_unidad->idtipo;
                    if($valor_unidad->id_proceso_confeccion <> 1){
                        $model->operacion = 2;
                    }
                    $model->save(false);
                    return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);
                }
            }
        }else{
            $model = new ValorPrendaUnidadDetalles();
            $model->id_valor = $id;                
            $model->idordenproduccion = $idordenproduccion;
            $model->dia_pago= date('Y-m-d');
            $model->id_planta = $id_planta;
            $model->id_tipo = $valor_unidad->idtipo;
            if($valor_unidad->id_proceso_confeccion <> 1){
                $model->operacion = 2;
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);
        }    
       
    }
    
    //PROCESO QUE BUSCA EL MODULO Y TRAE LAS EMPLEADOS
    
     public function actionNuevodetallemodular($id, $idordenproduccion, $id_planta)
    {              
       $fecha_corte = date('Y-m-d'); 
        $balanceo = \app\models\Balanceo::find()->where(['=','idordenproduccion', $idordenproduccion])->orderBy('id_balanceo asc')->all();
        if ($balanceo){
            foreach ($balanceo as $val):
                //este bloque busca las unidades confeccionadas por fecha
                $cantidad = \app\models\CantidadPrendaTerminadas::find()->where(['=','id_balanceo', $val->id_balanceo])->andWhere(['=','fecha_entrada', $fecha_corte])->all();
                if($cantidad){
                    $suma = 0; $total = 0;
                    foreach ($cantidad as $contar):
                       $suma += $contar->cantidad_terminada; 
                    endforeach;
                    $total = round($suma / $val->cantidad_empleados); 
                    //este proceso busca los operarios que estan en el modulo
                    $detalle_balanceo = \app\models\BalanceoDetalle::find()->where(['=','id_balanceo', $val->id_balanceo])->orderBy('id_operario asc')->all();
                    $operario = 0; $variable = 0;
                    if($detalle_balanceo){
                        foreach ($detalle_balanceo as $detalle):
                               $operario = $detalle->id_operario;
                               if($variable <> $operario){
                                $operario = $detalle->id_operario; 
                                 $variable = $operario;
                                 $valor_prenda = new ValorPrendaUnidadDetalles();
                                 $valor_prenda->id_operario = $operario;
                                 $valor_prenda->id_valor = $id;
                                 $valor_prenda->dia_pago= $fecha_corte;
                                 $valor_prenda->hora_inicio_modulo = $val->hora_inicio;
                                 $valor_prenda->idordenproduccion = $idordenproduccion;
                                 $valor_prenda->cantidad = $total; 
                                 $valor_prenda->id_planta = $id_planta;
                                 $valor_prenda->save(false);
                              }
                        endforeach;
                    }else{
                         $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion,'id_planta' => $id_planta]);
                         Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene asignado empleados para las operaciones.'); 
                    }    
                }else{
                   $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
                   Yii::$app->getSession()->setFlash('error', 'El modulo de balanceo Nro: '. $val->id_balanceo. ', no realizo confeccion el dia '.$fecha_corte.'. Favor hacer este proceso manual.'); 
                }    
            endforeach;
           return $this->redirect(['view', 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
        }else{
             $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
             Yii::$app->getSession()->setFlash('error', 'La orden de produccion Nro: '. $idordenproduccion. ', no tiene balanceo creado en sistema.!');
        }
    }
   
    //proceso de carga el pago de nomina
    
    public function actionPagarserviciosoperarios() {
        
        $model = new \app\models\FormPagarServicioOperario();
        $planta = \app\models\PlantaEmpresa::find()->all();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()){
                if (isset($_POST["crearfechaspago"])) {
                   $datosPago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $model->fecha_inicio])
                                                                      ->andWhere(['=','fecha_corte', $model->fecha_corte])
                                                                      ->andWhere(['=','id_planta', $model->id_planta])->one(); 
                    $fecha_inicio = $model->fecha_inicio;
                    $fecha_corte = $model->fecha_corte;     
                    $bodega = $model->id_planta;     
                    if($datosPago){
                       $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
                    }else{
                        $operario = Operarios::find()->where(['=','aplica_nomina_modulo', 1])
                                                     ->andWhere(['=','estado', 1])
                                                     ->andWhere(['=','id_planta', $model->id_planta ])->all();
                       
                        foreach ($operario as $operarios):
                            $tabla = new \app\models\PagoNominaServicios();
                            $tabla->id_operario = $operarios->id_operario;
                            if($operarios->homologar_document == 0){
                                $tabla->documento = $operarios->documento;
                            }else{
                                $tabla->documento = $operarios->documento_pago_banco;
                            }
                            
                            $tabla->operario = utf8_encode($operarios->nombrecompleto);
                            $tabla->fecha_inicio = $model->fecha_inicio;
                            $tabla->fecha_corte = $model->fecha_corte;
                            $tabla->observacion = $model->observacion;
                            $tabla->id_planta = $operarios->id_planta;
                            $tabla->usuariosistema = Yii::$app->user->identity->username;
                            $tabla->save(false);
                        endforeach;
                       
                         $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
                    }    
                }
                 
            }
        }
        
        return $this->renderAjax('pagarserviciosoperario', [
            'model' => $model,  
            'planta' => ArrayHelper::map($planta, "id_planta", "nombre_planta"),
        ]);      
    }
    
    //metodo que llama los pagos del servicio
    
    public function actionPageserviceoperario($fecha_inicio, $fecha_corte, $bodega) {
        if (isset($_POST["id_pago"])) {
            $intIndice = 0;
            $matricula = \app\models\Matriculaempresa::findOne(1);
            $configuracion_salario = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
            foreach ($_POST["id_pago"] as $intCodigo):
                $pago = \app\models\PagoNominaServicios::findOne($intCodigo);
                $buscarPagos = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $pago->id_operario])
                                                           ->andWhere(['>=','dia_pago', $fecha_inicio])
                                                           ->andWhere(['<=','dia_pago', $fecha_corte]) ->all();
                $contador = 0; $con = 0;
                $auxiliar = '';
                foreach ($buscarPagos as $valores):
                    $contador += $valores->vlr_pago;
                    if ($auxiliar <> $valores->dia_pago){
                        $con += 1; 
                        $auxiliar = $valores->dia_pago;
                    }else{
                        $auxiliar = $valores->dia_pago;
                    }
                endforeach;
                $pago->total_pagar = $contador;
                $pago->total_dias = $con;
                $pago->save(false);
                //codigo para insertar devengados
                $buscar = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $intCodigo])->one();
                if (!$buscar){
                    $detalle_pago = new \app\models\PagoNominaServicioDetalle();
                    $detalle_pago->id_pago = $intCodigo;
                    $detalle_pago->codigo_salario = $matricula->codigo_salario;
                    $detalle_pago->devengado = $contador;
                    $detalle_pago->fecha_corte = $fecha_corte;
                    $detalle_pago->save(false);
                    //codigo para insertar creditos
                    $credito = \app\models\CreditoOperarios::find()->where(['=','id_operario', $pago->id_operario])
                                                                   ->andWhere(['>','saldo_credito', 0])
                                                                   ->andWhere(['=','estado_credito', 1])->all();
                    if($credito){
                        foreach ($credito as $descuento):
                            $configuracion = \app\models\ConfiguracionCredito::find()->where(['=','codigo_credito', $descuento->codigo_credito])->one();
                            $detalle_credito = new \app\models\PagoNominaServicioDetalle();
                            $detalle_credito->id_pago = $intCodigo;
                            $detalle_credito->codigo_salario = $configuracion->codigo_salario;
                            $detalle_credito->deduccion = $descuento->vlr_cuota;
                            $detalle_credito->id_credito = $descuento->id_credito;
                            $detalle_credito->fecha_corte = $fecha_corte;
                            $detalle_credito->save(false);   
                        endforeach;
                    }
                   //codigo que inserta el auxilio de transporte
                    if($matricula->aplica_auxilio == 1){
                        $pagoBuscar = \app\models\PagoNominaServicios::findOne($intCodigo);
                        if($pagoBuscar->total_pagar > $matricula->base_auxilio){
                            $detalle_auxilio = new \app\models\PagoNominaServicioDetalle();
                            $detalle_auxilio->id_pago = $intCodigo;
                            $detalle_auxilio->codigo_salario = $matricula->codigo_salario_auxilio;
                            $detalle_auxilio->devengado = round(($configuracion_salario->auxilio_transporte_actual / 30) * $pagoBuscar->total_dias);
                            $detalle_auxilio->save(false);   
                        }
                    }
                }    
            endforeach;
            return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
         }
         return $this->render('pageserviceoperario', ['fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]);
    }
    //METODO QUE ACTUALIZA SALDO DE LA NOMINA DE CONFECCION
    
    public function actionActualizarsaldo($fecha_corte, $fecha_inicio, $bodega){
        
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])
                                                       ->andWhere(['=','id_planta', $bodega])->all(); 
        foreach ($pago as $pagoNomina):
            $pagoDetalle = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $pagoNomina->id_pago])->all();
            $deduccion = 0;
            $devengado = 0;
            foreach ($pagoDetalle as $detalle):
                 $devengado += $detalle->devengado;
                 $deduccion += $detalle->deduccion;
            endforeach;
            $pagoNomina->devengado = $devengado;
            $pagoNomina->deduccion = $deduccion;
            $pagoNomina->total_pagar = $devengado - $deduccion;
            $pagoNomina->save(false);
        endforeach;
        $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
    }
    
    //CODIGO QUE AUTORIZA LA NOMINA
    public function actionAutorizarnomina($fecha_corte, $fecha_inicio, $bodega) {
        $pago = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])->andWhere(['=','fecha_corte', $fecha_corte])
                                                       ->andWhere(['=','id_planta', $bodega])->orderBy('operario')->all();
        foreach ($pago as $autorizar):
            $autorizar->autorizado = 1;
            $autorizar->save(false);
            
            // codigo que busca si tiene credito todos
            $detallePago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $autorizar->id_pago])->all();  
            foreach ($detallePago as $detalle):
                  $credito = \app\models\CreditoOperarios::find()->where(['=','id_credito', $detalle->id_credito])->one();
                  if ($credito){
                      $table_abono = new \app\models\AbonoCreditoOperarios();
                      $table_abono->id_credito = $credito->id_credito;
                      $table_abono->vlr_abono = $detalle->deduccion;
                      $table_abono->saldo = $credito->saldo_credito - $table_abono->vlr_abono;
                      $table_abono->cuota_pendiente = ($credito->numero_cuotas - $credito->numero_cuota_actual) - 1;
                      $table_abono->observacion = $autorizar->observacion;
                      $table_abono->usuariosistema = Yii::$app->user->identity->username;
                      $table_abono->save(false);
                     $credito->numero_cuota_actual = $credito->numero_cuota_actual + 1;
                     $credito->saldo_credito = $credito->saldo_credito - $table_abono->vlr_abono;
                     $credito->save(false);
                  }
            endforeach;
        endforeach;
        return $this->redirect(["pageserviceoperario", 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega]); 
    }
    
    
    //CODIGO QUE VA AL DETALLE DEL PAGO
    
    public function actionVistadetallepago($id_pago, $fecha_corte, $fecha_inicio, $autorizado, $bodega,  $token = 1) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'bodega' => $bodega,
                    'token' => $token,
                    
        ]);
    }
    
    //PERMITE CONSULTAR LAS COLILLAS DE PAGO
    public function actionConsultadetallepago($id_pago, $fecha_corte, $fecha_inicio, $bodega, $autorizado, $token = 2) {
        $model = \app\models\PagoNominaServicios::findOne($id_pago);
        $detalle_pago = \app\models\PagoNominaServicioDetalle::find()->where(['=','id_pago', $model->id_pago])->orderBy('devengado asc')->all();
        return $this->render('vista_detalle_pago', [
                    'model' => $model,
                    'detalle_pago' => $detalle_pago,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_corte' => $fecha_corte,
                    'autorizado' => $autorizado,
                    'id_pago' => $id_pago,
                    'bodega' => $bodega,
                    'token' => $token,
                    
        ]);
    }
    
    
    //ESTE CODIGO EDITAR EL DETALLE DEL PAGO
    public function actionEditarvistadetallepago($id_pago, $id_detalle, $fecha_inicio, $bodega, $fecha_corte, $autorizado) {
        
        $model = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        if ($model->load(Yii::$app->request->post())) {
            $tabla = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
            $tabla->deduccion = $model->deduccion;
            $tabla->devengado = $model->devengado;
            $tabla->save(false);
            return $this->redirect(['valor-prenda-unidad/vistadetallepago','id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'autorizado' => $autorizado, 'bodega' => $bodega]);
        }
        return $this->render('editar_vista_detalle_pago', [
            'fecha_corte' => $fecha_corte,
            'fecha_inicio' => $fecha_inicio,
            'model' => $model,
            'id_pago' => $id_pago,
            'bodega' => $bodega,
            'id_detalle' => $id_detalle,
            'autorizado' => $autorizado,
        ]);
    }
   // codigo que permite agregar mas concepto de salario
    
    public function actionImportarconceptosalarios($id_pago, $fecha_inicio, $fecha_corte, $bodega, $autorizado)
    {
        $pilotoDetalle = \app\models\ConceptoSalarios::find()->Where(['=','adicion', 1])
                                                        ->andWhere(['=','tipo_adicion', 1])
                                                        ->andWhere(['=','debito_credito', 0]) 
                                                        ->orderBy('nombre_concepto asc')->all();
        $form = new \app\models\FormMaquinaBuscar();
        $q = null;
        $mensaje = '';
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $q = Html::encode($form->q);                                
                if ($q){
                    $pilotoDetalle = \app\models\ConceptoSalarios::find()
                            ->where(['like','nombre_concepto',$q])
                            ->orwhere(['like','codigo_salario',$q])
                            ->orderBy('nombre_concepto asc')
                            ->all();
                }               
            } else {
                $form->getErrors();
            }                    
        } else {
            $pilotoDetalle = \app\models\ConceptoSalarios::find()->Where(['=','adicion', 1])
                                                        ->andWhere(['=','tipo_adicion', 1])
                                                        ->andWhere(['=','debito_credito', 0]) 
                                                        ->orderBy('nombre_concepto asc')->all();
        }
        if (isset($_POST["codigo_salario"])) {
           $intIndice = 0;
            foreach ($_POST["codigo_salario"] as $intCodigo) {
                $table = new \app\models\PagoNominaServicioDetalle();
               // $detalle = PilotoDetalleProduccion::find()->where(['id_proceso' => $intCodigo])->one();
                $table->id_pago = $id_pago;
                $table->codigo_salario = $intCodigo;
                $table->devengado = 0;
                $table->deduccion = 0;
                $table->save(false);                                                
            }
           $this->redirect(["valor-prenda-unidad/vistadetallepago", 'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' => $bodega, 'autorizado' => $autorizado]);
        }
        return $this->render('importarconceptosalarios', [
            'pilotoDetalle' => $pilotoDetalle,            
            'mensaje' => $mensaje,
            'id_pago' => $id_pago,
            'fecha_inicio' => $fecha_inicio,
            'fecha_corte' => $fecha_corte,
            'autorizado' => $autorizado,
            'bodega' => $bodega,
            'form' => $form,

        ]);
    }
    
    // PROCESO QUE ELIMINE EL DETALLE DEL PAGO
    
     public function actionEliminardetallepago($id_pago,$id_detalle, $fecha_inicio,$bodega, $fecha_corte, $autorizado)
    {                                
        $detalle = \app\models\PagoNominaServicioDetalle::findOne($id_detalle);
        $detalle->delete();
        return $this->redirect(["vistadetallepago",'id_pago' => $id_pago, 'fecha_inicio' => $fecha_inicio, 'fecha_corte' => $fecha_corte, 'bodega' =>$bodega, 'autorizado' => $autorizado]);        
    }
    //proceso que imprime la colilla de confeccion
    
    public function actionImprimircolillaconfeccion($id_pago, $fecha_inicio, $fecha_corte)
    {                                
      //   $model = \app\models\PagoNominaServicios::findOne($id_pago);
        
         return $this->render('../formatos/colillapagoconfeccion', [
              'model' => \app\models\PagoNominaServicios::findOne($id_pago),
             'fecha_inicio' => $fecha_inicio,
             'fecha_corte' => $fecha_corte,
        ]);
    }
    
    public function actionEliminar($id,$detalle, $idordenproduccion, $id_planta, $tipo_pago)
    {                                
        try {
            $detalle = ValorPrendaUnidadDetalles::findOne($detalle);
            $detalle->delete();
            $this->Totalpagar($id);
            $this->TotalCantidades($id, $tipo_pago);
            return $this->redirect(["valor-prenda-unidad/view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
        } catch (IntegrityException $e) {  
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["valor-prenda-unidad/view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
        }catch (\Exception $e) { 
             Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
             return $this->redirect(["valor-prenda-unidad/view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
        }
    }
    //ELIMINA OPERACIONES CARGADAS 
    public function actionEliminar_operacion_cargada($id,$detalle, $idordenproduccion, $id_planta, $tipo_pago, $id_datalle_talla, $codigo, $tokenPlanta) {
        try {
            $detalle = ValorPrendaUnidadDetalles::findOne($detalle);
            $unidades = $detalle->cantidad;
            $this->DescargarUnidadConfeccionada($id_datalle_talla, $unidades);
            $detalle->delete();
            return $this->redirect(["view_search_operaciones",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago, 'codigo' => $codigo, 'tokenPlanta' => $tokenPlanta]);        
               
        } catch (Exception $ex) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["view_search_operaciones",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago, 'codigo' => $codigo, 'tokenPlanta' => $tokenPlanta]); 
              
        }catch (\Exception $e) { 
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["view_search_operaciones",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago, 'codigo' => $codigo, 'tokenPlanta' => $tokenPlanta]);        
        }
    }
    
    //ELIMINA LINEAS QUE TIEN TALLAS 
    public function actionEliminar_linea_operacion($id,$detalle, $idordenproduccion, $id_planta, $tipo_pago, $id_datalle_talla) {
        try {
            $detalle = ValorPrendaUnidadDetalles::findOne($detalle);
            $unidades = $detalle->cantidad;
            $this->DescargarUnidadConfeccionada($id_datalle_talla, $unidades);
            $detalle->delete();
            return $this->redirect(["view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
               
        } catch (Exception $ex) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]); 
              
        }catch (\Exception $e) { 
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["view",'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta'=> $id_planta, 'tipo_pago' => $tipo_pago]);        
        }
    }
    
    //PROCESO QUE QUE DESCARGAR DEL DETALLE DE LA ORDEN DE PRODUCCION
    protected function DescargarUnidadConfeccionada($id_datalle_talla, $unidades) {
        $restar = 0;
        $detalle_orden = \app\models\Ordenproducciondetalle::findOne($id_datalle_talla);
        $restar = $detalle_orden->cantidad_confeccionada - $unidades;
        $detalle_orden->cantidad_confeccionada = $restar;
        $detalle_orden->save(false);
        
    }
    
    //ELIMINA EL REGISTRO D EPAGO
    
    public function actionEliminarpago($id, $fecha_inicio, $fecha_corte, $bodega)
    {                                
        try {
            $detalle = \app\models\PagoNominaServicios::findOne($id);
            $detalle->delete();
            Yii::$app->getSession()->setFlash('success', 'Registro Eliminado con éxito.');
            return $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
        } catch (IntegrityException $e) {
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
            
        } catch (\Exception $e) {        
             Yii::$app->getSession()->setFlash('error', 'Error al eliminar este registro, tiene registros asociados en otros procesos');
            return $this->redirect(["valor-prenda-unidad/pageserviceoperario",'fecha_inicio' => $fecha_inicio, 'fecha_corte' =>$fecha_corte, 'bodega' => $bodega]);
        }
    }
    
    protected function Totalpagar($id) {
        $valor = ValorPrendaUnidad::findOne($id);
        $detalle = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->all();
        $suma=0; 
        $ajuste = 0;
        $operacion = 0;
        foreach ($detalle as $val):
            if($val->operacion == 0){
               $suma += $val->costo_dia_operaria;
            }else{
                if($val->operacion == 1){
                    $operacion += $val->costo_dia_operaria;
                }else{
                    $ajuste += $val->costo_dia_operaria;
                }
            }   
        endforeach;
        $valor->total_confeccion = $suma;
        $valor->total_operacion = $operacion;
        $valor->total_ajuste = $ajuste;
        $valor->total_pagar = $suma + $operacion + $ajuste;
        $valor->save(false);
    }
    //actualiza las cantidades
    protected function TotalCantidades($id, $tipo_pago) {
        $valor = ValorPrendaUnidad::findOne($id);
        $detalle = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->all();
        $suma=0; $operacion = 0;
            foreach ($detalle as $val):
                if($val->operacion == 0){
                   $suma += $val->cantidad;
                }else{
                    if(($val->operacion == 1)){
                       $operacion += $val->cantidad; 
                    }
                }
            endforeach;
            $valor->cantidad_procesada = $suma;
            $valor->cantidad_operacion = $operacion;
            $valor->save(false);
        if($valor->cantidad_procesada > $valor->cantidad  || $valor->cantidad_operacion > $valor->cantidad){
            if($tipo_pago == 1){   
                Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $valor->idordenproduccion. '.');
            }    
        } 
       
    }
    
    public function actionAutorizado($id, $idordenproduccion, $id_planta, $tipo_pago,$tokenPlanta) {
        $model = $this->findModel($id);
        if($tipo_pago == 1){
            if($model->cantidad_procesada > $model->cantidad  || $model->cantidad_operacion > $model->cantidad){
                 Yii::$app->getSession()->setFlash('error', 'La cantidad y/o operacion procesada es mayor que las unidades entradas en la orden Nro: '. $model->idordenproduccion. '.');
                 return $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            }else{  
                if ($model->autorizado == 0) {                        
                    $model->autorizado = 1;            
                    $model->update();
                    $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);  
                } else{
                    $model->autorizado = 0;
                    $model->update();
                    return $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' =>$id_planta, 'tipo_pago' => $tipo_pago]);  
                }
            }    
        }else{
            if ($model->autorizado == 0) {                        
                $model->autorizado = 1;
                $model->update();
                $this->TotalizarCostoPrenda($id);
                return $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago, 'tokenPlanta' => $tokenPlanta]);
                
            } else {
                $model->autorizado = 0;
                $model->update();
                return $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago, 'tokenPlanta' => $tokenPlanta]);
            }
        }    
    }
    
    //PROCESO QUE TOTALIZA EL COSTO DEL VALOR PRENDA
    protected function TotalizarCostoPrenda($id) {
        $model = $this->findModel($id);
        $detalle_prenda = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])->all();
        $costo1 = 0; $costo2 = 0; $total = 0;
        foreach ($detalle_prenda as $key => $valor) {
            $operario = Operarios::findOne($valor->id_operario);
            if($operario->vinculado == 1){
                $costo1 += $valor->vlr_pago;
            }else{
                $costo2 += $valor->vlr_pago;
            }
        }
        $total = round(($costo1 * 38)/100);
        $model->total_pagar = $total + $costo1 + $costo2;
        $model->save(false);
    }
    
     //PROCESO QUE TOTALIZA EL COSTO DEL VALOR PRENDA
    protected function TotalizarCostoTallas($id, $id_detalle) {
        $model = \app\models\Ordenproducciondetalle::findOne($id_detalle);
        $detalle_prenda = ValorPrendaUnidadDetalles::find()->where(['=','iddetalleorden', $id_detalle])->all();
        $costo1 = 0; $costo2 = 0; $total = 0;
        foreach ($detalle_prenda as $key => $valor) {
            $operario = Operarios::findOne($valor->id_operario);
            if($operario->vinculado == 1){
                $costo1 += $valor->vlr_pago;
            }else{
                $costo2 += $valor->vlr_pago;
            }
        }
        
        $model->costo_confeccion =  $costo1 + $costo2;
        $model->save(false);
    }
    public function actionCerrarpago($id, $idordenproduccion, $id_planta, $tipo_pago, $tokenPlanta) {
            $model = $this->findModel($id);
            $orden = Ordenproduccion::findOne($idordenproduccion);
            $model->cerrar_pago =  1;
            $model->estado_valor = 1;
            $model->save(false);
            if ($tipo_pago == 1) {
                return $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            } else {
                return $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago, 'tokenPlanta' => $tokenPlanta]);
            }
    }
    //cerrar el pago y la orden de produccion
    
    public function actionCerrarpagoorden($id, $idordenproduccion , $id_planta, $tipo_pago, $tokenPlanta) {
           $model = $this->findModel($id);
           $orden = Ordenproduccion::findOne($idordenproduccion);
           $model->cerrar_pago = 1;
           $model->estado_valor = 1;
           $model->save(false);
           $orden->pagada = 1;
           $orden->save(false);
           if ($tipo_pago == 1) {
                return $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago]);
            } else {
               return $this->redirect(["valor-prenda-unidad/search_tallas_ordenes", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta, 'tipo_pago' => $tipo_pago, 'tokenPlanta' => $tokenPlanta]);
            }
    }
    
    //PRCESO QUE APLICA REGLA
    
    public function actionAplicar_regla($id, $idordenproduccion, $id_planta) {
        $valores = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])
                                                    ->andWhere(['=','aplica_regla', 0])->andWhere(['=','aplica_sabado', 0])->orderBy('id_operario ASC')->all();
        if (count($valores)== 0){
            $valores = ValorPrendaUnidadDetalles::find()->where(['=','id_valor', $id])
                                            ->andWhere(['=','aplica_sabado', 1])->andWhere(['=','aplica_regla', 0])->orderBy('id_operario ASC')->all(); 
        }
        if (isset($_POST["consecutivo"])) {
            $intIndice = 0;
            $auxiliar = 0; $suma = 0; $total = 0;
            $matricula = \app\models\Matriculaempresa::findOne(1);
            $regla = \app\models\ReglaComisiones::find()->where(['=','estado_regla', 1])->one();
            foreach ($_POST["consecutivo"] as $intCodigo) {
                $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                $auxiliar = $table->control_fecha;
                $operario = Operarios::findOne($table->id_operario);
                $consulta = ValorPrendaUnidadDetalles::find()->where(['=','id_operario', $operario->id_operario])
                                                             ->andWhere(['=','dia_pago', $table->dia_pago])->all();
                $contador = 0;
                if($table->aplica_regla == 0 && $table->aplica_sabado == 0){
                    foreach ($consulta as $valor){
                       $contador += $valor->porcentaje_cumplimiento;
                    }
                    if($contador > $regla->porcentaje_cumplimiento){
                        foreach ($consulta as $val):
                            $tabla = ValorPrendaUnidadDetalles::findOne($val->consecutivo);
                            if($operario->vinculado == 1){
                                $total =0;
                                $total = (($val->vlr_prenda / $matricula->vlr_minuto_vinculado) *($regla->valor_minuto_vinculado));
                                $tabla->vlr_prenda = round($total,0);
                                $tabla->vlr_pago = $tabla->cantidad * $total;
                                $tabla->aplica_regla = 1;
                                $tabla->save(false);
                            }else{
                                $total =0;
                                $total = (($val->vlr_prenda/$matricula->vlr_minuto_contrato) *($regla->valor_minuto_contrato));
                                $tabla->vlr_prenda = round($total, 0);
                                $tabla->vlr_pago = $tabla->cantidad * $total;
                                $tabla->costo_dia_operaria =  $tabla->vlr_pago;
                                $tabla->aplica_regla = 1;
                                $tabla->save(false);
                            } 
                             $intIndice++;   
                        endforeach;
                    }else{
                        $table->aplica_regla = 1;
                        $table->save(false);
                    }    
                }else{
                    $table->aplica_regla = 1;
                    $table->save(false);
                }
                $intIndice++;   
            }
            return $this->redirect(["valor-prenda-unidad/view", 'id' => $id, 'idordenproduccion' => $idordenproduccion, 'id_planta' => $id_planta]);
        }
        
        return $this->render('_aplicar_regla', [
            'valores' => $valores,            
            'id' => $id,
            'idordenproduccion' => $idordenproduccion,
            'id_planta' => $id_planta,

        ]);
    }
    
    //aplicar porcentaje a al prenda- genera comision adicional
    public function actionAplicarporcentajeprenda()
    {
        if (Yii::$app->user->identity){   
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso', 130])->all()){
                $form = new ModelAplicarPorcentaje();
                $planta = null;
                $fecha_inicio = null;
                $fecha_corte = null;
                $porcentaje = null;
                $tipo = null;
                $model = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $planta = Html::encode($form->planta);
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $porcentaje = Html::encode($form->porcentaje);
                        $tipo = Html::encode($form->tipo_empleado);
                        $pagos = ValorPrendaUnidadDetalles::find()->where(['=','aplica_sabado', 1])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_inicio])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_corte])
                                                                  ->andWhere(['=', 'id_planta', $planta])
                                                                  ->andWhere(['=', 'aplicar_porcentaje', 0])->all();
                        $model = $pagos;
                    }else {
                        $form->getErrors();
                    }
                } 
                if (isset($_POST["aplicarporcentaje"])) {
                    if (isset($_POST["consecutivo"])) {
                        $intIndice = 0;
                        $calcular = 0;
                        $cont = 0;
                        foreach ($_POST["consecutivo"] as $intCodigo) {
                           $table = ValorPrendaUnidadDetalles::findOne($intCodigo);
                           if($tipo == 0){
                               $calcular = round(($table->vlr_pago * $porcentaje)/100);   
                               $table->vlr_pago = $calcular + $table->vlr_pago; 
                               $table->costo_dia_operaria = $table->vlr_pago;
                               $table->aplicar_porcentaje = 1;
                               $table->save(false);
                               $cont += 1;
                           }else{
                                $calcular = round(($table->vlr_pago * $porcentaje)/100);   
                                $table->vlr_pago = $calcular + $table->vlr_pago; 
                                $table->aplicar_porcentaje = 1;
                                $table->save(false);
                                $cont += 1;
                           }
                        $calcular = 0;  
                        $intIndice++; 
                        }
                        $pagos = ValorPrendaUnidadDetalles::find()->where(['=','aplica_sabado', 1])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_inicio])
                                                                  ->andWhere(['=', 'dia_pago', $fecha_corte])
                                                                  ->andWhere(['=', 'aplicar_porcentaje', 0])->all();
                        $model = $pagos;
                        if($tipo == 0){
                            Yii::$app->getSession()->setFlash('info', 'Se aplico el porcentaje del '.  $porcentaje.'%  a ' . $cont . ' Operarios de la empresa.');
                                return $this->render('aplicarporcentajeprenda', [
                                  'form' => $form,
                                  'model' => $model,
                                  'tipo' => $tipo,
                                ]);
                        }else{
                            Yii::$app->getSession()->setFlash('info', 'Se aplico el porcentaje del '.  $porcentaje.'%  a ' . $cont . '  Operarios de la empresa.');
                                return $this->render('aplicarporcentajeprenda', [
                                  'form' => $form,
                                  'model' => $model,
                                  'tipo' => $tipo,
                                ]);
                        }
                    }
                }   
                //terminac el proceso de aplicar porcentaje
                return $this->render('aplicarporcentajeprenda', [
                       'form' => $form,
                       'model' => $model,
                       'tipo' => $tipo,
               ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }
    
     //BUSCAR OPERACIONES POR TALLA
   public function actionSearch_operacion_talla($token = 1) {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',132])->all()){
            $form = new \app\models\FormFiltroConsultaFichaoperacion();
            $idcliente = null;
            $ordenproduccion = null;
            $desde = null;
            $codigoproducto = null;
            $hasta = null;
            $clientes = \app\models\Cliente::find()->orderBy('nombrecorto ASC')->all();
            
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $idcliente = Html::encode($form->idcliente);
                    $ordenproduccion = Html::encode($form->ordenproduccion);
                    $codigoproducto = Html::encode($form->codigoproducto);
                    $desde = Html::encode($form->desde);
                    $hasta = Html::encode($form->hasta);
                    $table = Ordenproduccion::find()
                            ->andFilterWhere(['=', 'idcliente', $idcliente])
                            ->andFilterWhere(['between', 'fechallegada', $desde, $hasta])
                            ->andFilterWhere(['=', 'idordenproduccion', $ordenproduccion])
                            ->andFilterWhere(['=', 'codigoproducto', $codigoproducto])
                            ->orderBy('idordenproduccion desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 30,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaficha($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Ordenproduccion::find()
                        ->orderBy('idordenproduccion desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 30,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsultaficha($tableexcel);
                }
            }

            return $this->render('index_operacion_tallas', [
                        'model' => $model,
                        'form' => $form,
                        'token' => $token,
                        'pagination' => $pages,
                        'clientes' => ArrayHelper::map($clientes, "idcliente", "nombrecorto"),
            ]);
         }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
        
   // LISTADO DE OPERACIONES POR TALLA
   public function actionListado_operaciones($id, $id_detalle, $token){
       $model = \app\models\Ordenproducciondetalle::find()->where(['=','iddetalleorden', $id_detalle])->one();
       $operaciones = \app\models\FlujoOperaciones::find()->where(['=','idordenproduccion', $id])->all();
       return $this->render('listado_operacion', [
                        'operaciones' => $operaciones,
                        'id'=> $id,   
                        'model' => $model,
                        'id_detalle' => $id_detalle,
                        'token' => $token,
                                   
            ]);
   }
   
    // LISTADO DE OPERACIONES POR TALLA
    public function actionListado_operarios($id, $id_detalle, $id_operacion, $token){
       $model = \app\models\FlujoOperaciones::find()->where(['=','idproceso', $id_operacion])->one();
       $operaciones = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $id])
                                                ->andWhere(['=','iddetalleorden', $id_detalle])
                                                ->andwhere(['=','idproceso', $id_operacion])->all();
       return $this->render('listado_operarios', [
                        'operaciones' => $operaciones,
                        'id'=> $id,   
                        'model' => $model,
                        'id_detalle' => $id_detalle,
                        'id_operacion' => $id_operacion,
                        'token' => $token,                                   
            ]);
    }
   //maestro detalle de consulta por opreaciones
     public function actionMaestro_operaciones() {
        $form = new \app\models\FormFiltroMaestroOperaciones();
        //creando vectores de busqueda
        $modelo = 0;
        $sw = 0;   $pages = null;
        $idordenproduccion = null;
        $id_operario = null;
        $iddetalleorden = null;
        $idproceso = null;
        $orden = Ordenproduccion::find()->orderBy('idordenproduccion DESC')->all();
        $operarios = Operarios::find()->orderBy('nombrecompleto DESC')->all();
        if ($form->load(Yii::$app->request->get())) {
            $id_operario = Html::encode($form->id_operario);
            $idordenproduccion = Html::encode($form->idordenproduccion);
            $iddetalleorden = Html::encode($form->iddetalleorden);
            $idproceso = Html::encode($form->idproceso);
            if($idordenproduccion > 0){
                $sw = 1; 
            }
            
            if($form->idordenproduccion <> 0){
                $table = ValorPrendaUnidadDetalles::find()
                            ->andFilterWhere(['=', 'id_operario', $id_operario])
                            ->andFilterWhere(['=', 'idordenproduccion', $idordenproduccion])
                            ->andFilterWhere(['=', 'iddetalleorden', $iddetalleorden])
                            ->andFilterWhere(['=', 'idproceso', $idproceso]);
                $table = $table->orderBy('idproceso, iddetalleorden, id_operario ASC');
                $tableexcel = $table->all();
                $count = clone $table;
                $to = $count->count();
                $pages = new Pagination([
                    'pageSize' => 25,
                    'totalCount' => $count->count()
                ]);
                $modelo = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){                            
                                $check = isset($_REQUEST['iddetalleorden DESC']);
                                $this->actionExcelOperaciones($tableexcel);
                            }
                       
            }else{
                Yii::$app->getSession()->setFlash('warning', 'Debe se seleccion una ORDEN DE PRODUCCION para ejecutar la consulta.');
            }             
        }
        return $this->render('search_maestro_operaciones', [
                'form' =>$form,
                'modelo' => $modelo,
                'sw' => $sw,
                'pagination' => $pages,
                'orden' => ArrayHelper::map($orden, 'idordenproduccion', 'OrdenValorPrenda'),
                'operarios' => ArrayHelper::map($operarios, 'id_operario', 'nombrecompleto'),
        ]);
    }
    
    //EXPORTA A EXCEL LA CONSULTA DE TODOS LOS PAGOS
     public function actionPagoservicioconfeccion($fecha_corte, $fecha_inicio, $bodega) {        
        $model = \app\models\PagoNominaServicios::find()->where(['=','fecha_inicio', $fecha_inicio])
                                                        ->andWhere(['=','fecha_corte', $fecha_corte])
                                                        ->andWhere(['=','id_planta', $bodega])->orderBy([ 'operario' =>SORT_ASC ])->all();
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'No PAGO')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA INICIO')
                    ->setCellValue('E2', 'FECHA CORTE')
                    ->setCellValue('F2', 'FECHA PROCESO')
                    ->setCellValue('G2', 'No DIAS')
                    ->setCellValue('H2', 'USUARIO')
                    ->setCellValue('I2', 'AUTORIZADO')
                    ->setCellValue('J2', 'DEVENGADO')
                    ->setCellValue('K2', 'DEDUCCION')
                    ->setCellValue('L2', 'TOTAL PAGAR')
                    ->setCellValue('M2', 'OBSERVACION')
                    ->setCellValue('N2', 'PLANTA');
                  
        $i = 3;
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_pago)
                    ->setCellValue('B' . $i, $val->documento)
                    ->setCellValue('C' . $i, $val->operario)
                    ->setCellValue('D' . $i, $val->fecha_inicio)
                    ->setCellValue('E' . $i, $val->fecha_corte)
                    ->setCellValue('F' . $i, $val->fecha_registro)
                    ->setCellValue('G' . $i, $val->total_dias)
                    ->setCellValue('H' . $i, $val->usuariosistema)
                    ->setCellValue('I' . $i, $val->autorizado)
                    ->setCellValue('J' . $i, $val->devengado)
                    ->setCellValue('K' . $i, $val->deduccion)
                    ->setCellValue('L' . $i, $val->Total_pagar)
                    ->setCellValue('M' . $i, $val->observacion)
                    ->setCellValue('N' . $i, $val->planta->nombre_planta);

              
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Total pagar');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Valor_Nomina.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
    
   //EXPORTAR LOS OPERARIOS QUE HACEN LAS OPERARACIONES Y LA OPERACION
    public function actionExpotar_cantidad_confeccionada($id, $id_detalle, $id_operacion) {        
        $model = ValorPrendaUnidadDetalles::find()->where(['=','idordenproduccion', $id])
                                                ->andWhere(['=','iddetalleorden', $id_detalle])
                                                ->andwhere(['=','idproceso', $id_operacion])->all();
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'ID')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA CONFECCION')
                    ->setCellValue('E2', 'CANTIDAD')
                    ->setCellValue('F2', 'OP INTERNA')
                    ->setCellValue('G2', 'VR. OPERACION')
                    ->setCellValue('H2', 'VR PAGADO')
                    ->setCellValue('I2', '% CUMPLIMIENTO')
                    ->setCellValue('J2', 'CODIGO')
                    ->setCellValue('K2', 'NOMBRE OPERACION')
                    ->setCellValue('L2', 'PLANTA');
                                  
        $i = 3;
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->consecutivo)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto)
                    ->setCellValue('D' . $i, $val->dia_pago)
                    ->setCellValue('E' . $i, $val->cantidad)
                    ->setCellValue('F' . $i, $val->idordenproduccion)
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('J' . $i, $val->idproceso)
                    ->setCellValue('K' . $i, $val->operaciones->proceso)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta);
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Listado_operarios_confeccion.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
    
    //EXCEL QUE ESPORTAR LOS PAGOS DE NOMINA
    public function actionExcelPagoPrendaServicio($tableexcel) {        
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'No PAGO')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO')
                    ->setCellValue('D2', 'FECHA INICIO')
                    ->setCellValue('E2', 'FECHA CORTE')
                    ->setCellValue('F2', 'FECHA PROCESO')
                    ->setCellValue('G2', 'No DIAS')
                    ->setCellValue('H2', 'USUARIO')
                    ->setCellValue('I2', 'AUTORIZADO')
                    ->setCellValue('J2', 'DEVENGADO')
                    ->setCellValue('K2', 'DEDUCCION')
                    ->setCellValue('L2', 'TOTAL PAGAR')
                    ->setCellValue('M2', 'OBSERVACION')
                    ->setCellValue('N2', 'PLANTA');
                  
        $i = 3;
        foreach ($tableexcel as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_pago)
                    ->setCellValue('B' . $i, $val->documento)
                    ->setCellValue('C' . $i, $val->operario)
                    ->setCellValue('D' . $i, $val->fecha_inicio)
                    ->setCellValue('E' . $i, $val->fecha_corte)
                    ->setCellValue('F' . $i, $val->fecha_registro)
                    ->setCellValue('G' . $i, $val->total_dias)
                    ->setCellValue('H' . $i, $val->usuariosistema)
                    ->setCellValue('I' . $i, $val->autorizado)
                    ->setCellValue('J' . $i, $val->devengado)
                    ->setCellValue('K' . $i, $val->deduccion)
                    ->setCellValue('L' . $i, $val->total_pagar)
                    ->setCellValue('M' . $i, $val->observacion)
                    ->setCellValue('N' . $i, $val->planta->nombre_planta);
              
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Total pagar');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Valor_Nomina.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
   
    //DESCARGA EL LISTADO DE LAS OPERACIONES
    public function actionExcelOperaciones($tableexcel) {        
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true); 
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', 'CODIGO')
                    ->setCellValue('B2', 'OPERACION')
                    ->setCellValue('C2', 'TALLA')
                    ->setCellValue('D2', 'OPERARIO')
                    ->setCellValue('E2', 'OP')
                    ->setCellValue('F2', 'FECHA CONFECCION')
                    ->setCellValue('G2', 'UNIDAD X TALLA')
                    ->setCellValue('H2', 'CANT. CONFECCIONADA')
                    ->setCellValue('I2', 'VR. OPERACION')
                    ->setCellValue('J2', 'TOTAL PAGO')
                    ->setCellValue('K2', 'PLANTA')
                    ->setCellValue('L2', 'SERVICIO')
                    ->setCellValue('M2', 'USUARIO');
                    
                  
        $i = 3;
        $previousIdProceso = null; // 
        $colSpanCount = 12;
        foreach ($tableexcel as $val) {  
            if ($previousIdProceso !== null && $val->idproceso !== $previousIdProceso) {
               $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idproceso);
                    if($val->idproceso == null){
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, 'REGISTRO NO ENCONTRATO');
                    }else{
                        $objPHPExcel->setActiveSheetIndex(0)
                       ->setCellValue('B' . $i, $val->operaciones->proceso);    
                    }            
                    if($val->iddetalleorden == null){
                       $objPHPExcel->setActiveSheetIndex(0)
                       ->setCellValue('C' . $i, 'REGISTRO NO ENCONTRATO');        
                    }else{
                       $objPHPExcel->setActiveSheetIndex(0) 
                       ->setCellValue('C' . $i, $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla);        
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('D' . $i, $val->operarioProduccion->nombrecompleto)
                    ->setCellValue('E' . $i, $val->idordenproduccion)
                    ->setCellValue('F' . $i, $val->dia_pago);
                    if($val->iddetalleorden == null){        
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('G' . $i, 'REGISTRO NO ENCONTRATO');                 
                    }else{
                       $objPHPExcel->setActiveSheetIndex(0) 
                       ->setCellValue('G' . $i, $val->detalleOrdenProduccion->cantidad);    
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('H' . $i, $val->cantidad)
                    ->setCellValue('I' . $i, $val->vlr_prenda)
                    ->setCellValue('J' . $i, $val->vlr_pago)
                    ->setCellValue('K' . $i, $val->planta->nombre_planta)
                    ->setCellValue('L' . $i, $val->tipoproceso->tipo)
                    ->setCellValue('M' . $i, $val->usuariosistema);
                     $previousIdProceso = $val->idproceso;
                   
            $i++;                        
        }

        $objPHPExcel->getActiveSheet()->setTitle('Total pagar');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Listado_operaciones.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
    
    public function actionGenerarexcel($id) {        
        $ficha = ValorPrendaUnidad::findOne($id);
        $model = ValorPrendaUnidadDetalles::find()->where(['=','id_valor',$id])->orderBy([ 'id_operario' =>SORT_ASC ])->all();
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'PAGO DE OPERACIONES')
                    ->setCellValue('A2', 'ORDEN')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO(A)')
                    ->setCellValue('D2', 'OPERACION')
                    ->setCellValue('E2', 'DIA PAGO')
                    ->setCellValue('F2', 'CANTIDAD')
                    ->setCellValue('G2', 'VR. PRENDA')
                    ->setCellValue('H2', 'VR. PAGO')
                    ->setCellValue('I2', 'VR. COSTO')
                    ->setCellValue('J2', '% CUMPLIMIENTO')
                    ->setCellValue('K2', 'USUARIO')
                    ->setCellValue('L2', 'PLANTA')
                    ->setCellValue('M2', 'OBSERVACION');
                  
        $i = 3;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $ficha->idordenproduccion)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto);
                    if($val->operacion == 0){
                         $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('D' . $i, $confeccion);
                    }else{
                        if($val->operacion == 1){
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $operaciones);
                        }else{
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $ajuste);
                        }
                    } 
                     $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E' . $i, $val->dia_pago)
                    ->setCellValue('F' . $i, $val->cantidad)
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->costo_dia_operaria)
                    ->setCellValue('J' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta)
                    ->setCellValue('M' . $i, $val->observacion);
              
                   
            $i++;                        
        }
        //promedio por dia
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("           
           SELECT SUM(valor_prenda_unidad_detalles.vlr_pago) AS Total, valor_prenda_unidad_detalles.id_operario FROM valor_prenda_unidad_detalles WHERE id_valor = ".$id."  GROUP BY id_operario");
        $result = $command->queryAll();
        $i = 3;
       /* foreach ($result as $promedio){
            $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('K' . $i, $promedio['Total'])
                     ->setCellValue('L' . $i, $promedio['id_operario']);   
            $i++;            
        }*/
        //fin promedio por dia

        $objPHPExcel->getActiveSheet()->setTitle('Total_pago_prendas');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Total_pago_prendas.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
    
    public function actionGenerar_excel_talla($id, $id_detalle) {        
        $ficha = ValorPrendaUnidad::findOne($id);
        $model = ValorPrendaUnidadDetalles::find()->where(['=','id_valor',$id])->andWhere(['=','iddetalleorden', $id_detalle])->orderBy('id_operario ASC')->all();
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->mergeCells("a".(1).":l".(1));
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'PAGO DE OPERACIONES')
                    ->setCellValue('A2', 'ORDEN')
                    ->setCellValue('B2', 'DOCUMENTO')
                    ->setCellValue('C2', 'OPERARIO(A)')
                    ->setCellValue('D2', 'OPERACION')
                    ->setCellValue('E2', 'DIA PAGO')
                    ->setCellValue('F2', 'CANTIDAD')
                    ->setCellValue('G2', 'VR. PRENDA')
                    ->setCellValue('H2', 'VR. PAGO')
                    ->setCellValue('I2', 'VR. COSTO')
                    ->setCellValue('J2', '% CUMPLIMIENTO')
                    ->setCellValue('K2', 'USUARIO')
                    ->setCellValue('L2', 'PLANTA')
                    ->setCellValue('M2', 'TALLA')
                    ->setCellValue('N2', 'OPERACION')
                    ->setCellValue('O2', 'OBSERVACION');
                  
        $i = 3;
        $confeccion = 'CONFECCION';
        $operaciones = 'OPERACIONES';
        $ajuste = 'AJUSTE';
        foreach ($model as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $ficha->idordenproduccion)
                    ->setCellValue('B' . $i, $val->operarioProduccion->documento)
                    ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto);
                    if($val->operacion == 0){
                         $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue('D' . $i, $confeccion);
                    }else{
                        if($val->operacion == 1){
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $operaciones);
                        }else{
                             $objPHPExcel->setActiveSheetIndex(0)
                             ->setCellValue('D' . $i, $ajuste);
                        }
                    } 
                     $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E' . $i, $val->dia_pago)
                    ->setCellValue('F' . $i, $val->cantidad)
                    ->setCellValue('G' . $i, $val->vlr_prenda)
                    ->setCellValue('H' . $i, $val->vlr_pago)
                    ->setCellValue('I' . $i, $val->costo_dia_operaria)
                    ->setCellValue('J' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->planta->nombre_planta)
                    ->setCellValue('M' . $i, $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('N' . $i, $val->operaciones->proceso)
                    ->setCellValue('O' . $i, $val->observacion);
              
                   
            $i++;                        
        }
        //promedio por dia
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("           
           SELECT SUM(valor_prenda_unidad_detalles.vlr_pago) AS Total, valor_prenda_unidad_detalles.id_operario FROM valor_prenda_unidad_detalles WHERE id_valor = ".$id."  GROUP BY id_operario");
        $result = $command->queryAll();
        $i = 3;
       /* foreach ($result as $promedio){
            $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('K' . $i, $promedio['Total'])
                     ->setCellValue('L' . $i, $promedio['id_operario']);   
            $i++;            
        }*/
        //fin promedio por dia

        $objPHPExcel->getActiveSheet()->setTitle('Listados');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Total_pago_talla.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0 
        header("Content-Transfer-Encoding: binary ");
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);        
        $objWriter->save('php://output');
        //$objWriter->save($pFilename = 'Descargas');
        exit; 
        
    }
   
    public function actionExcelResumeValorPrenda($tableexcel) {
    // Inicialización del objeto PHPExcel y configuración de propiedades del documento
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties()
        ->setCreator("EMPRESA")
        ->setLastModifiedBy("EMPRESA")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");

    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);

    // Definición de las columnas y sus encabezados en un array asociativo
    $columnas = [
        'A' => 'ID',
        'B' => 'ORD. PRODUCCION',
        'C' => 'OPERARIO',
        'D' => 'CODIGO',
        'E' => 'OPERACION',
        'F' => 'FECHA PROCESO',
        'G' => 'SAM',
        'H' => 'CANTIDAD',
        'I' => 'VR. PRENDA',
        'J' => 'TOTAL PAGADO',
        'K' => '% CUMPLIMIENTO',
        'L' => 'COSTO DIA',
        'M' => 'PLANTA',
        'N' => 'TALLA',
        'O' => 'HORA INICIO',
        'P' => 'HORA CORTE',
        'Q' => 'DIA SEMANA',
        'R' => 'HORA DESAYUNO',
        'S' => 'HORA ALMUERZO',
        'T' => 'SAM REAL CONFECCION',
        'U' => 'DIFERENCIA SAM',
        'V' => 'HORA EN DESUSO',
        'W' => 'USUARIO',
        'X' => 'ESTADO_REGISTRO',
        'Y' => 'OBSERVACION',
        'Z' => 'LINEA',
    ];

    // Establecer encabezados de columna y auto-ajustar el tamaño usando el array
    foreach ($columnas as $columna => $titulo) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columna)->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($columna . '1', $titulo);
    }
    
    // Mapeo del número del día de la semana a su nombre
    $diasSemana = [
        1 => 'LUNES',
        2 => 'MARTES',
        3 => 'MIERCOLES',
        4 => 'JUEVES',
        5 => 'VIERNES',
        6 => 'SABADO',
        7 => 'DOMINGO',
    ];

    $i = 2; // Fila inicial para los datos
    foreach ($tableexcel as $val) {
        // Asignación de valores a cada celda de forma secuencial y legible
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $val->consecutivo)
            ->setCellValue('B' . $i, $val->idordenproduccion)
            ->setCellValue('C' . $i, $val->operarioProduccion->nombrecompleto)
            ->setCellValue('D' . $i, $val->idproceso)
            ->setCellValue('E' . $i, $val->operaciones->proceso)
            ->setCellValue('F' . $i, $val->dia_pago)
            ->setCellValue('G' . $i, $val->minuto_prenda)
            ->setCellValue('H' . $i, $val->cantidad)
            ->setCellValue('I' . $i, $val->vlr_prenda)
            ->setCellValue('J' . $i, $val->vlr_pago)
            ->setCellValue('K' . $i, $val->porcentaje_cumplimiento)
            ->setCellValue('L' . $i, $val->costo_dia_operaria)
            ->setCellValue('M' . $i, $val->planta->nombre_planta)
            ->setCellValue('N' . $i, $val->detalleOrdenProduccion->productodetalle->prendatipo->talla->talla)
            ->setCellValue('O' . $i, $val->hora_inicio)
            ->setCellValue('P' . $i, $val->hora_corte);

        // Asignación del día de la semana usando el array de mapeo
        $dia_semana_nombre = $diasSemana[$val->dia_semana] ?? 'NOT FOUND';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $i, $dia_semana_nombre);

        // Continuación de la asignación de valores
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('R' . $i, $val->hora_inicio_desayuno)
            ->setCellValue('S' . $i, $val->hora_inicio_almuerzo)
            ->setCellValue('T' . $i, $val->tiempo_real_confeccion)
            ->setCellValue('U' . $i, $val->diferencia_tiempo)
            ->setCellValue('V' . $i, $val->hora_inicio_desuso)
            ->setCellValue('W' . $i, $val->usuariosistema)
            ->setCellValue('X' . $i, $val->registroPagado)
            ->setCellValue('Y' . $i, $val->observacion)
            ->setCellValue('Z' . $i, $val->hora_descontar);

        $i++;
    }

    $objPHPExcel->getActiveSheet()->setTitle('Resumen pago');
    $objPHPExcel->setActiveSheetIndex(0);

    // Cabeceras para la descarga del archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Resumen_pago.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save('php://output');
    exit;
}

    
    public function actionExcelconsultaValorPrenda($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'ORDEN PROD.')
                    ->setCellValue('C1', 'CLIENTE')
                    ->setCellValue('D1', 'SERVICIO')
                    ->setCellValue('E1', 'VR. VINCULADO')                    
                    ->setCellValue('F1', 'VR. CONTRATO')
                    ->setCellValue('G1', 'TOTAL CONFECCION')
                    ->setCellValue('H1', 'CANT. PROCESADA')
                    ->setCellValue('I1', 'TOTAL AJUSTE')
                    ->setCellValue('J1', 'TOTAL OPERACION')
                    ->setCellValue('K1', 'CANT. OPERACION')
                    ->setCellValue('L1', 'TOTAL PAGAR')
                    ->setCellValue('M1', 'CANTIDAD') 
                    ->setCellValue('N1', 'AUTORIZADO')
                    ->setCellValue('O1', 'CERRADO')
                    ->setCellValue('P1', 'ACTIVO')
                    ->setCellValue('Q1', 'PLANTA/BODEGA')
                    ->setCellValue('R1', 'F. PROCESO')
                    ->setCellValue('S1', 'USUARIO EDITADO')
                    ->setCellValue('T1', 'F. EDITADO');
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_valor)
                    ->setCellValue('B' . $i, $val->idordenproduccion)
                    ->setCellValue('C' . $i, $val->ordenproduccion->cliente->nombrecorto)
                    ->setCellValue('D' . $i, $val->tipo->tipo)                    
                    ->setCellValue('E' . $i, $val->vlr_vinculado)
                    ->setCellValue('F' . $i, $val->vlr_contrato)  
                    ->setCellValue('G' . $i, $val->total_confeccion)
                    ->setCellValue('H' . $i, $val->cantidad_procesada)
                    ->setCellValue('I' . $i, $val->total_ajuste)
                    ->setCellValue('J' . $i, $val->total_operacion)
                    ->setCellValue('K' . $i, $val->cantidad_operacion)
                    ->setCellValue('L' . $i, $val->total_pagar)
                    ->setCellValue('M' . $i, $val->cantidad)
                    ->setCellValue('N' . $i, $val->autorizadoPago)
                    ->setCellValue('O' . $i, $val->cerradoPago)
                    ->setCellValue('P' . $i, $val->estadovalor)
                    ->setCellValue('Q' . $i, $val->planta->nombre_planta)
                    ->setCellValue('R' . $i, $val->fecha_proceso)
                    ->setCellValue('S' . $i, $val->usuario_editado)
                    ->setCellValue('T' . $i, $val->fecha_editado);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Valor_prenda');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="valor_prendas.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
    
    //EXCEL QUE SACA EL INGRESO Y COSTO POR DIA
     public function actionExcelConsultaIngresos($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
  
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'FECHA CONFECCION')
                    ->setCellValue('B1', 'OPERARIO')
                    ->setCellValue('C1', 'OP INTERNA')
                    ->setCellValue('D1', 'OP CLIENTE')
                    ->setCellValue('E1', 'CLIENTE')
                    ->setCellValue('F1', 'REFERENCIA')                    
                    ->setCellValue('G1', 'OPERACION')
                    ->setCellValue('H1', 'CANTIDAD')
                    ->setCellValue('I1', 'SAM CONFECCION')
                    ->setCellValue('J1', '% CUMPLIMIENTO')
                    ->setCellValue('K1', 'INGRESOS')
                    ->setCellValue('L1', 'COSTOS');
                   
        $i = 2;
        $costo = 0;
        $valor = 0; $ingreso = 0; $total = 0; $total2 = 0;  
        $empresa = \app\models\Matriculaempresa::findOne(1);
        foreach ($tableexcel as $val) {
            if ($val->operarioProduccion->vinculado == 0) { // personal al contrato
                $valor = $val->vlr_prenda / $empresa->vlr_minuto_contrato;
            } else {
                $valor = $val->vlr_prenda / $empresa->vlr_minuto_vinculado;
                $costo = $val->costo_dia_operaria;
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->dia_pago)
                    ->setCellValue('B' . $i, $val->operarioProduccion->nombrecompleto)
                    ->setCellValue('C' . $i, $val->idordenproduccion)
                    ->setCellValue('D' . $i, $val->ordenproduccion->ordenproduccion)
                    ->setCellValue('E' . $i, $val->ordenproduccion->cliente->nombrecorto)
                    ->setCellValue('F' . $i, $val->ordenproduccion->codigoproducto);
                    if($val->idproceso == null){
                       $objPHPExcel->setActiveSheetIndex(0)
                       ->setCellValue('G' . $i, 'NO HAY TALLAS');
                    }else{
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('G' . $i, $val->operaciones->proceso);
                    } 
                    $objPHPExcel->setActiveSheetIndex(0)                    
                    ->setCellValue('H' . $i, $val->cantidad);
                    $tipoP = $val->ordenproduccion->tipoProducto;
                    $tipoProceso = $val->ordenproduccion->tipo;
                    if($tipoP){
                        $table = \app\models\ClientePrendas::find()->where(['=', 'id_tipo_producto', $tipoP->id_tipo_producto])->one();
                        if ($val->operarioProduccion->vinculado == 0) { //personal al contrato
                            if ($tipoProceso->idtipo == 2) { //proceso que busca  terminacion
                                $total = $valor * $table->valor_terminacion;
                                $total2 = ($total * $val->cantidad);
                            } else {
                                $total = $valor * $table->valor_confeccion;
                                $total2 = ($total * $val->cantidad);
                            }
                            $costo = $val->vlr_pago;
                        } else {
                            if ($tipoProceso->idtipo == 2) { //proceso que busca  terminacion
                                $total = $valor * $table->valor_terminacion;
                                $total2 = ($total * $val->cantidad);
                            } else {
                                $total = $valor * $table->valor_confeccion;
                                $total2 = ($total * $val->cantidad);
                            }
                        }
                    }
                     $objPHPExcel->setActiveSheetIndex(0)      
                    ->setCellValue('I' . $i, ''.number_format($valor,3))  
                    ->setCellValue('J' . $i, $val->porcentaje_cumplimiento)
                    ->setCellValue('K' . $i, round($total2))
                    ->setCellValue('L' . $i, $costo);
                  
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Resumen pago');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Resumen_pago.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }  
    
    //EXCEL QUE ENVIA LA BITACORA
     public function actionExcelconsultaBitacora($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
      
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'OP')
                    ->setCellValue('B1', 'REFERENCIA')
                    ->setCellValue('C1', 'TALLA')
                    ->setCellValue('D1', 'OPERARIO')
                    ->setCellValue('E1', 'OPERACION')                    
                    ->setCellValue('F1', 'FECHA CONFECCION')
                    ->setCellValue('G1', 'HORA DE CORTE')
                    ->setCellValue('H1', 'SAM')
                    ->setCellValue('I1', 'SAM FINAL')
                    ->setCellValue('J1', 'EFICIENCIA')
                    ->setCellValue('K1', 'NOTA')
                    ->setCellValue('L1', 'CLIENTE');
                    
        $i = 2;
        
        foreach ($tableexcel as $val) {
                $ComSam = \app\models\FlujoOperaciones::find()->where([
                                'idproceso' => $val->idproceso,
                                'idordenproduccion' => $val->idordenproduccion])->one();                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idordenproduccion)
                    ->setCellValue('B' . $i, $val->ordenproduccion->codigoproducto)
                    ->setCellValue('C' . $i, $val->detalleorden->productodetalle->prendatipo->talla->talla)
                    ->setCellValue('D' . $i, $val->operario->nombrecompleto)                    
                    ->setCellValue('E' . $i, $val->proceso->proceso)
                    ->setCellValue('F' . $i, $val->fecha_confeccion)  
                    ->setCellValue('G' . $i, $val->hora_corte)
                    ->setCellValue('H' . $i, $ComSam->minutos)
                    ->setCellValue('I' . $i, $val->tiempo_real_confeccion)
                    ->setCellValue('J' . $i, $val->porcentaje_eficiencia)
                    ->setCellValue('K' . $i, $val->concepto)
                    ->setCellValue('L' . $i, $val->ordenproduccion->cliente->nombrecorto);
                   
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Listado');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="bitacora_eficiencia.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }
}
