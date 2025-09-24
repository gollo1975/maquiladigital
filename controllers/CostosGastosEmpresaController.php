<?php

namespace app\controllers;
//modelos
use app\models\FormFiltroCostoGastosEmpresa;
use app\models\UsuarioDetalle;
use app\models\CostosGastosEmpresa;
use app\models\CantidadPrendaTerminadas;
use app\models\ProgramacionNomina;
use app\models\Matriculaempresa;
use app\models\CostosGastosEmpresaNomina;
//clases
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/**
 * CostosGastosEmpresaController implements the CRUD actions for CostosGastosEmpresa model.
 */
class CostosGastosEmpresaController extends Controller
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
     * Lists all CostosGastosEmpresa models.
     * @return mixed
     */
     public function actionIndex() {
        if (Yii::$app->user->identity) {
            if (UsuarioDetalle::find()->where(['=', 'codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=', 'id_permiso', 117])->all()) {
                $form = new FormFiltroCostoGastosEmpresa();
                $fecha_inicio = null;
                $fecha_corte = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $fecha_inicio = Html::encode($form->fecha_inicio);
                        $fecha_corte = Html::encode($form->fecha_corte);
                        $table = CostosGastosEmpresa::find()
                                ->andFilterWhere(['>=', 'fecha_inicio', $fecha_inicio])
                                ->andFilterWhere(['<=', 'fecha_corte', $fecha_corte]);
                        $table = $table->orderBy('id_costo_gasto DESC');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $modelo = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                        if (isset($_POST['excel'])) {
                            $check = isset($_REQUEST['id_costo_gasto  DESC']);
                            $this->actionExcelCostoGastos($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = CostosGastosEmpresa::find()
                             ->orderBy('id_costo_gasto DESC');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $modelo = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if (isset($_POST['excel'])) {
                        //$table = $table->all();
                        $this->actionExcelCostoGastos($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'modelo' => $modelo,
                            'form' => $form,
                            'pagination' => $pages,
                ]);
            } else {
                return $this->redirect(['site/sinpermiso']);
            }
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single CostosGastosEmpresa model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $empresa = \app\models\Matriculaempresa::findOne(1);
        $costo = CostosGastosEmpresa::findOne($id);
        $costoSeguridad = \app\models\CostoSeguridadsocial::find()->where(['=','id_costo_gasto', $id])->orderBy('empleado ASC')->all();
        $servicios = [];
        $prestacionServicio = [];
        if($empresa->aplica_modulo_compra == 0){
            if($costo->id_planta != null){
                 $servicios = \app\models\CostoFijoDetalle::find()->where(['=','id_planta', $costo->id_planta])->orderBy('descripcion ASC')->all();
                 $prestacionServicio = \app\models\PagoNominaServicios::find()->where(['>=','fecha_inicio', $costo->fecha_inicio])
                                                                         ->andWhere(['<=','fecha_corte', $costo->fecha_corte])
                                                                         ->andWhere(['=','id_planta', $costo->id_planta])->orderBy('operario ASC')->all();
            }else{
                 $servicios = \app\models\CostoFijoDetalle::find()->orderBy('descripcion ASC')->all();
                 $prestacionServicio = \app\models\PagoNominaServicios::find()->where(['>=','fecha_inicio', $costo->fecha_inicio])
                                                                         ->andWhere(['<=','fecha_corte', $costo->fecha_corte])->orderBy('operario ASC')->all();
            }
        }else{
            
            if($costo->id_planta != null){
                $servicios = \app\models\CostoFijoDetalle::find()->where(['=','id_planta', $costo->id_planta])
                                                                 ->andWhere(['=','aplica_concepto', 1])->orderBy('descripcion ASC')->all();
                $prestacionServicio = \app\models\PagoNominaServicios::find()->where(['>=','fecha_inicio', $costo->fecha_inicio])
                                                                         ->andWhere(['<=','fecha_corte', $costo->fecha_corte])
                                                                         ->andWhere(['=','id_planta', $costo->id_planta])->orderBy('operario ASC')->all();
            }else{
                $servicios = \app\models\CostoFijoDetalle::find()->andWhere(['=','aplica_concepto', 1])->orderBy('descripcion ASC')->all();
                $prestacionServicio = \app\models\PagoNominaServicios::find()->where(['>=','fecha_inicio', $costo->fecha_inicio])
                                                                         ->andWhere(['<=','fecha_corte', $costo->fecha_corte])->orderBy('operario ASC')->all();
            }
        }    
       
        $costo_nomina = \app\models\CostosGastosEmpresaNomina::find()->where(['=','id_costo_gasto', $id])->all();
        
        //PROCESO QUE ELIMINA SELECCION
        if (Yii::$app->request->post()) {
            if (isset($_POST["eliminar_seleccion"])) {
                if (isset($_POST["registro_seleccionados"])) {
                    $con = 0;
                    foreach ($_POST["registro_seleccionados"] as $intCodigo) {
                        try {
                            $eliminar = \app\models\CostoSeguridadsocial::findOne($intCodigo);
                            $eliminar->delete();
                            $con++;
                        } catch (IntegrityException $e) {

                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');
                        } catch (\Exception $e) {
                            Yii::$app->getSession()->setFlash('error', 'Error al eliminar el detalle, tiene registros asociados en otros procesos');

                        }
                    }
                    Yii::$app->getSession()->setFlash('success', 'Se eliminaron' .$con. ' registro enviados por el cliente.');
                    $this->redirect(["costos-gastos-empresa/view", 'id' => $id]);
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Debe seleccionar al menos un registro.');
                }    
             }
        }             
       
        return $this->render('view', [
            'model' => $this->findModel($id),
            'costo_nomina' => $costo_nomina,
            'servicios' => $servicios,
            'prestacionServicio' => $prestacionServicio,
            'costoSeguridad' => $costoSeguridad,
        ]);
    }
    
    
    
    //PROCESO QUE ACTUALIZA COSTO DE SEGURIDAD SOCIAL
    public function actionActualizar_registros($id) {
        $datosSeguridad = \app\models\CostoSeguridadsocial::find()->where(['=','id_costo_gasto', $id])->all();
        $pension = 0;
        $eps = 0;
        $arl = 0;
        $caja = 0;
        foreach ($datosSeguridad as $val):
            $pension = round($val->salario_prestacional * $val->porcentaje_pension)/100;
            $eps = round($val->salario_prestacional * $val->porcentaje_eps)/100;
            $arl = round($val->salario_prestacional * $val->porcentaje_arl)/100;
            $caja = round($val->salario_prestacional * $val->porcentaje_caja)/100;
            $val->pension = $pension;
            $val->eps = $eps;
            $val->arl = $arl;
            $val->caja_compensacion = $caja;
            $val->save(false);
        endforeach;
        $this->SumarCostoSeguridad($id);
        return $this->redirect(['view', 'id' => $id]);
    }
        
        
    //SUBPROCESO QUE TOTALIZA LOS COSTOS DE SEGURIDAD SOCIAL
    protected function SumarCostoSeguridad($id) {
        $costoGasto = CostosGastosEmpresa::findOne($id);
        $conCosto = \app\models\CostoSeguridadsocial::find()->where(['=','id_costo_gasto', $id])->all();
        $totalPension = 0; $totalEps = 0; $totalArl = 0;
        $totalCaja = 0; $totalCosto = 0;
        foreach ($conCosto as $dato):
            $totalPension += $dato->pension;
            $totalEps += $dato->eps;
            $totalArl += $dato->arl;
            $totalCaja += $dato->caja_compensacion;
        endforeach;
        $totalCosto = $totalPension + $totalEps + $totalArl + $totalCaja;  
        $costoGasto->total_seguridad_social = $totalCosto;
        $costoGasto->save(false);
    }
    
    /**
     * Updates an existing CostosGastosEmpresa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $planta = \app\models\PlantaEmpresa::find()->all();
        $grupoPago = \app\models\GrupoPago::find()->where(['estado' => 1])->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_costo_gasto]);
        }
        $costo = \app\models\CostosGastosEmpresaNomina::find()->where(['=','id_costo_gasto', $id])->one();
        if($costo){ 
            $this->redirect(["index"]);
        }else{
           return $this->render('update', [
            'model' => $model,
            'planta' => ArrayHelper::map($planta, 'id_planta', 'nombre_planta'),
            'grupoPago' => ArrayHelper::map($grupoPago, 'id_grupo_pago', 'grupo_pago'),
           ]);
        }
    }

//PROCESO QUE CREA EL REGISTRO
    
    public function actionGenerarcostogastos() {
        $model = new \app\models\FormCostoGastoEmpresa();
        $planta = \app\models\PlantaEmpresa::find()->all();
        $grupoPago = \app\models\GrupoPago::find()->where(['estado' => 1])->all();
         if ($model->load(Yii::$app->request->post())) {
               if ($model->validate()){
                    if (isset($_POST["generargastos"])) {
                        $empresa = \app\models\Matriculaempresa::findOne(1);
                        $conCosto = CostosGastosEmpresa::find()->where(['=','fecha_inicio', $model->fecha_inicio])
                                                               ->andWhere(['=','fecha_corte', $model->fecha_corte])
                                                               ->andWhere(['=','id_planta', $model->id_planta])->one(); 
                        if($conCosto){
                            return $this->redirect(["index"]); 
                        }else{
                            $table = new CostosGastosEmpresa();
                            $table->fecha_inicio = $model->fecha_inicio;
                            $table->fecha_corte = $model->fecha_corte;
                            $table->usuariosistema = Yii::$app->user->identity->username;
                            $table->observacion = $model->observacion;
                            $table->id_planta = $model->id_planta;
                            $table->id_grupo_pago = $model->grupo_pago;
                            $table->periodo = $model->periodo;
                            $table->id = $empresa->id;
                            $table->save();
                            return $this->redirect(["index"]); 
                        }    
                    }
               } 
         }
        return $this->renderAjax('generarcostogastos', [
            'model' => $model,    
            'planta' => ArrayHelper::map($planta, 'id_planta', 'nombre_planta'),
            'grupoPago' => ArrayHelper::map($grupoPago, 'id_grupo_pago', 'grupo_pago'),
        ]);    
    }
    
    //permite generar la nomina del personal seleccionado
    public function actionGenerar_seleccion_empleados($id) {
         $costo = CostosGastosEmpresa::findOne($id);
        $modelo = \app\models\ProgramacionNomina::find()->where([
                                                            'id_grupo_pago' => $costo->id_grupo_pago])
                                                        ->andWhere(['between','fecha_desde', $costo->fecha_inicio, $costo->fecha_corte])
                                                        ->andWhere(['=','id_tipo_nomina', 1])->all();
        
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $empleadosSeleccionados = Yii::$app->request->post('empleados_seleccionados');

            if (empty($empleadosSeleccionados) || !is_array($empleadosSeleccionados)) {
                return ['status' => 'error', 'message' => 'No se ha seleccionado ningún empleado para guardar.'];
            }

            $registros_guardados = 0;
            $errores = 0;
            
            // Recorre la lista de IDs de empleados
            foreach ($empleadosSeleccionados as $intCodigo) {
                
                // Busca el registro de la programación de nómina
                $registro = ProgramacionNomina::findOne($intCodigo);
                
                if ($registro) {
                    try {
                        // Carga la información de la empresa
                        $empresa = Matriculaempresa::findOne(1);
                        $cesantias = 0;
                        $primas = 0;
                        $intereses = 0;
                        $vacacion = 0;
                       
                        // Lógica para el cálculo de cesantías y primas
                        if ($registro->contrato->auxilio_transporte == 0) {
                            $cesantias = (($registro->ibc_prestacional + $registro->total_auxilio_transporte) * $empresa->porcentaje_cesantias) / 100;
                            $primas = (($registro->ibc_prestacional + $registro->total_auxilio_transporte) * $empresa->porcentaje_prima) / 100;
                            $intereses = ($cesantias * 1)/100;
                            $vacacion = ($registro->ibc_prestacional * $empresa->porcentaje_vacacion)/100;
                        } else {
                            $cesantias = (($registro->ibc_prestacional * $empresa->porcentaje_cesantias) / 100);
                            $primas = (($registro->ibc_prestacional * $empresa->porcentaje_prima) / 100);
                            $intereses = ($cesantias * $empresa->porcentaje_intereses)/100;
                            $vacacion = ($registro->ibc_prestacional * $empresa->porcentaje_vacacion)/100;
                        }
                        
                        // Crea una nueva instancia del modelo a guardar
                        $table = new CostosGastosEmpresaNomina();
                        
                        // Asigna los valores a las propiedades del modelo
                        $table->id_costo_gasto = $id;
                        $table->salarios = $registro->total_devengado;
                        $table->cesantias = round($cesantias);
                        $table->primas = round($primas);
                        $table->intereses = round($intereses);
                        $table->vacacion = round($vacacion);
                        $table->ajuste = round(($vacacion * $empresa->ajuste_caja)/100);
                        $table->usuariosistema = Yii::$app->user->identity->username;
                        // Intenta guardar el modelo
                        if ($table->save(false)) { 
                            $registros_guardados++;
                           
                        } else {
                            $errores++;
                            Yii::error('Error al guardar: ' . print_r($table->getErrors(), true));
                        }
                    } catch (\Exception $ex) {
                        $errores++;
                        // Devuelve el mensaje de la excepción inmediatamente
                        return ['status' => 'error', 'message' => "Error al guardar el empleado con ID: {$intCodigo}. Detalles: " . $ex->getMessage()];
                    }
                }
            }
         
            
            // Devuelve una respuesta JSON con el resultado.
            if ($errores > 0) {
                 return ['status' => 'error', 'message' => "No se pudieron guardar {$errores} registros."];
            } else {
                 return ['status' => 'success', 'message' => "Se han guardado {$registros_guardados} empleados correctamente."];
            }
           
          return $this->redirect(['view',
                'id' => $id,
            ]);
        }
        
        return $this->renderAjax('genera_nomina_seleccion', [
            'modelo' => $modelo,    
        ]);    
    }
    
    //proceso que acumula los costos de nomina
    protected function SumarCostosNominaSucursal($id) {
        $buscar = CostosGastosEmpresaNomina::find()->where(['=','id_costo_gasto', $id])->all();
        $costo = CostosGastosEmpresa::findOne($id);
        $total_valor = 0;
        
        foreach ($buscar as $val) {
            $total_valor += $val->salarios + $val->cesantias + $val->intereses + $val->primas + $val->vacacion + $val->ajuste;
        }
      echo  $costo->total_nomina =  $total_valor;
        $costo->save(false);
                
    }
    
    //PROCESO QUE GENERA EL COSTO DE NOMINA
    public function actionGenerarcostonomina($id, $fecha_inicio, $fecha_corte) {
       
        $costos = CostosGastosEmpresa::findOne($id);
        $salario = \app\models\ConfiguracionSalario::find()->where(['=','estado', 1])->one();
        $gasto_nomina = \app\models\CostosGastosEmpresaNomina::find()->where(['=','id_costo_gasto', $id])->one();
        if($costos->id_grupo_pago == null){
            $nomina = \app\models\ProgramacionNomina::find()->where(['=','estado_cerrado', 1])->andWhere(['>=','fecha_desde', $fecha_inicio])
                                                       ->andWhere(['<=','fecha_hasta', $fecha_corte])->all();  
        }else{
             $nomina = \app\models\ProgramacionNomina::find()->where(['=','estado_cerrado', 1])->andWhere(['between','fecha_desde', $fecha_inicio, $fecha_corte])
                                                       ->andWhere(['=','id_grupo_pago', $costos->id_grupo_pago])->all();  
        }    
       if(count($nomina) > 0){
           $configuracionEmpresa = \app\models\Matriculaempresa::findOne(1);
           $conSalario = 0; $conInteres = 0; $conPrima = 0;
           $conCesantia =0; $conVacacion = 0; $conPrestacional = 0;
           $total = 0;
           $conAjuste = 0;
           foreach ($nomina as $valorNomina):
               $conSalario += $valorNomina->total_devengado;
               $conPrestacional += $valorNomina->ibc_prestacional; 
           endforeach;
           //calculos
           $conCesantia = round(($conPrestacional + $salario->auxilio_transporte_actual) * $configuracionEmpresa->porcentaje_cesantias)/100;
           $conInteres = round($conCesantia * $configuracionEmpresa->porcentaje_intereses)/100;
           $conPrima = round(($conPrestacional + $salario->auxilio_transporte_actual) * $configuracionEmpresa->porcentaje_prima)/100;
           $conVacacion = round($conPrestacional * $configuracionEmpresa->porcentaje_vacacion)/100;
           $conAjuste = round($conVacacion * $configuracionEmpresa->ajuste_caja)/100;
           if($gasto_nomina){
                $gasto_nomina->salarios = $conSalario;
                $gasto_nomina->cesantias = $conCesantia;
                $gasto_nomina->intereses = $conInteres;
                $gasto_nomina->primas = $conPrima;
                $gasto_nomina->vacacion = $conVacacion;
                $gasto_nomina->ajuste = $conAjuste;
                $gasto_nomina->save(false);
                $costos->total_nomina = $conSalario + $conCesantia + $conInteres + $conPrima + $conVacacion + $conAjuste;
                $costos->save(false);
                return $this->redirect(['view', 'id' => $id]);
           }else{
                $table = new \app\models\CostosGastosEmpresaNomina();
                $table->id_costo_gasto = $id;
                $table->salarios = $conSalario;
                $table->cesantias = $conCesantia;
                $table->intereses = $conInteres;
                $table->primas = $conPrima;
                $table->vacacion = $conVacacion;
                $table->ajuste = $conAjuste;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->save(false);
                $costos->total_nomina = $conSalario + $conCesantia + $conInteres + $conPrima + $conVacacion + $conAjuste;
                $costos->save(false);
                return $this->redirect(['view', 'id' => $id]);
           }    
       }else{
           Yii::$app->getSession()->setFlash('warning', 'No existen registros en este periodo de cierre, favor validar las fechas.');
       }
        return $this->redirect(['view', 'id' => $id]);
    }
    //PROCESO DE GENERA EL COSTO DE SEGURIDAD SOCIAL
    
    public function actionGenerarcostoseguridad($id, $fecha_inicio, $fecha_corte) {
        $model = $this->findModel($id);
        if($model->id_grupo_pago !=null ){
            $nomina = \app\models\ProgramacionNomina::find()->where(['=','estado_cerrado', 1])->andWhere(['>=','fecha_desde', $fecha_inicio])
                                                        ->andWhere(['<=','fecha_hasta', $fecha_corte])
                                                        ->andwhere(['=','id_grupo_pago', $model->id_grupo_pago])->all(); 
        }else{
           $nomina = \app\models\ProgramacionNomina::find()->where(['=','estado_cerrado', 1])->andWhere(['>=','fecha_desde', $fecha_inicio])
                                                       ->andWhere(['<=','fecha_hasta', $fecha_corte])->all();  
        }
        
        
     
        $configuracion_pension = \app\models\ConfiguracionPension::findOne(1);
        $configuracion_eps = \app\models\ConfiguracionEps::findOne(3);
        if(count($nomina) > 0){
            foreach ($nomina as $listado):
                $contrato = \app\models\Contrato::findOne($listado->id_contrato);
                $table = new \app\models\CostoSeguridadsocial();
                $table->id_costo_gasto = $id;
                $table->documento = $listado->cedula_empleado;
                $table->empleado = $listado->empleado->nombrecorto;
                $table->salario_prestacional = $listado->ibc_prestacional;
                $table->porcentaje_pension = $configuracion_pension->porcentaje_empleador;
                $table->porcentaje_eps = $configuracion_eps->porcentaje_empleador_eps;
                $table->porcentaje_arl = $contrato->arl->arl;
                $table->porcentaje_caja = $contrato->cajaCompensacion->porcentaje_caja;
                $table->usuariosistema = Yii::$app->user->identity->username;
                $table->save(false);
            endforeach;
              return $this->redirect(['view', 'id' => $id]);
        }else{
           Yii::$app->getSession()->setFlash('warning', 'No existen registros de nomina en este periodo de cierre, favor validar las fechas.'); 
           
        }  
       return $this->redirect(['view', 'id' => $id]);
    }
    
    //ACTUALIZA COSTO SE SEGURIDAD SOCIAL.
    public function actionAutorizarcostos($id) {
       $costos = CostosGastosEmpresa::findOne($id);
       $this->SumarCostosNominaSucursal($id);
       if($costos->autorizado == 0){
           $costos->autorizado = 1;
           $costos->total_costos = $costos->total_nomina + $costos->total_seguridad_social  + $costos->servicios + $costos->gastos_fijos + $costos->compras;
           $costos->save(false);
           $this->SumarIngresosEmpresa($id);
           return $this->redirect(['view', 'id' => $id]);
       }else{
           $costos->autorizado = 0;
           $costos->save(false);
           return $this->redirect(['view', 'id' => $id]);
       }
    }
    //proceso que busca las compras
    public function actionGenerarcompras($id){
        $costos = CostosGastosEmpresa::findOne($id);
        $empresa = \app\models\Matriculaempresa::findOne(1);
        if($empresa->aplica_modulo_compra == 0){
            $compra = \app\models\Compra::find()->where(['=','id_tipo_compra', 1])
                                                ->andWhere(['>=','fechainicio', $costos->fecha_inicio])
                                                ->andWhere(['<=','fechainicio', $costos->fecha_corte])->all();
        }else{
          $compra = \app\models\Compra::find()->Where(['between','fechainicio', $costos->fecha_inicio, $costos->fecha_corte])
                                              ->andWhere(['=','id_planta', $costos->id_planta])  ->all();  
        }    
        $subtotal = 0;
        if($compra){    
            foreach ($compra as $compras):
                 $subtotal += $compras->subtotal;
            endforeach;
        }    
        $costos->compras = $subtotal;
        $costos->save(false);
        return $this->redirect(['view', 'id' => $id]);
    }
    
    
    //PROCESO QUE SUMA LOS INGRESOS DEL MES
    protected function SumarIngresosEmpresa($id) {
        $costos = CostosGastosEmpresa::findOne($id);
        if($costos->id_planta != null){
            $prendas = CantidadPrendaTerminadas::find()->where(['>=','fecha_entrada', $costos->fecha_inicio])
                                                   ->andWhere(['<=','fecha_entrada', $costos->fecha_corte])
                                                   ->andWhere(['=','id_planta', $costos->id_planta]) ->all();
        }else{
            $prendas = CantidadPrendaTerminadas::find()->where(['>=','fecha_entrada', $costos->fecha_inicio])
                                                   ->andWhere(['<=','fecha_entrada', $costos->fecha_corte])->all();
        }    
        $suma = 0;
        if(count($prendas) > 0){
            foreach ($prendas as $ingresos):
                $suma += $ingresos->detalleorden->vlrprecio * $ingresos->cantidad_terminada;
            endforeach;
            
            $costos->total_ingresos = $suma;
            $costos->save(false);
        }
    }
    /**
     * Deletes an existing CostosGastosEmpresa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionEliminar_registro($id, $id_detalle)
    {
        $detalle = \app\models\CostosGastosEmpresaNomina::findOne($id_detalle);
        $detalle->delete();
        return $this->redirect(['view',
            'model' => $this->findModel($id),
            'id' => $id,
            ]);
    }
    //ELIMINA TODO DE LA SEGURIDAD SOCIAL
    public function actionEliminar_todo_seguridad_social($id)
    {
       $detalle = \app\models\CostoSeguridadsocial::find()->where(['=','id_costo_gasto', $id])->all();
            
        foreach ($detalle as $val){
            try {
                $val->delete();
                Yii::$app->getSession()->setFlash('success', 'Se eliminaron todos los registros.');
            } catch (IntegrityException $e) {
                Yii::$app->getSession()->setFlash('error', 'Error al eliminar la programacion de nomina, tiene registros asociados en otros procesos de la nómina');
            } catch (\Exception $e) {
                Yii::$app->getSession()->setFlash('error', 'Error al eliminar la programacion de nomina, tiene registros asociados en otros procesos');
            }
        } 
        return $this->redirect(['costos-gastos-empresa/view',
            'id' => $id,
            'model' => $this->findModel($id),
            ]);
    }   

    /**
     * Finds the CostosGastosEmpresa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CostosGastosEmpresa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CostosGastosEmpresa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
     public function actionExcelCostoGastos($tableexcel) {        
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
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'FECHA INICIO')
                    ->setCellValue('C1', 'FECHA CORTE')
                    ->setCellValue('D1', 'VR. NOMINA')
                    ->setCellValue('E1', 'VR. SEGURIDAD SOCIAL')
                    ->setCellValue('F1', 'VR. SERVICIOS')
                    ->setCellValue('G1', 'VR. GASTOS FIJOS')
                    ->setCellValue('H1', 'TOTAL COSTOS')
                    ->setCellValue('I1', 'TOTAL INGRESOS')
                    ->setCellValue('J1', 'FECHA PROCESO')
                    ->setCellValue('K1', 'USUARIO')
                    ->setCellValue('L1', 'OBSERVACION');
                  
        $i = 2;
        foreach ($tableexcel as $val) {                            
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->id_costo_gasto)
                    ->setCellValue('B' . $i, $val->fecha_inicio)
                    ->setCellValue('C' . $i, $val->fecha_corte)
                    ->setCellValue('D' . $i, $val->total_nomina)
                    ->setCellValue('E' . $i, $val->total_seguridad_social)
                    ->setCellValue('F' . $i, $val->servicios)
                    ->setCellValue('G' . $i, $val->gastos_fijos)
                    ->setCellValue('H' . $i, $val->total_costos)
                    ->setCellValue('I' . $i, $val->total_ingresos)
                    ->setCellValue('J' . $i, $val->fecha_proceso)
                    ->setCellValue('K' . $i, $val->usuariosistema)
                    ->setCellValue('L' . $i, $val->observacion);
              
                   
            $i++;                        
        }
        //promedio por dia
      /*  $connection = Yii::$app->getDb();
        $command = $connection->createCommand("           
           SELECT SUM(valor_prenda_unidad_detalles.vlr_pago) AS Total, valor_prenda_unidad_detalles.id_operario FROM valor_prenda_unidad_detalles WHERE id_valor = ".$id."  GROUP BY id_operario");
        $result = $command->queryAll();
        $i = 3;*/
     

        $objPHPExcel->getActiveSheet()->setTitle('Costos_gastos');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="Costos_Gastos.xlsx"');
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
}
