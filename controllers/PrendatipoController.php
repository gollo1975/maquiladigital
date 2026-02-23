<?php

namespace app\controllers;

use Yii;
use app\models\Prendatipo;
use app\models\PrendatipoSearch;
use app\models\Talla;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
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
use app\models\UsuarioDetalle;

/**
 * PrendatipoController implements the CRUD actions for Prendatipo model.
 */
class PrendatipoController extends Controller
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
     * Lists all Prendatipo models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',10])->all()){
                $searchModel = new PrendatipoSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Displays a single Prendatipo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Prendatipo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Prendatipo();
       
        if ($model->load(Yii::$app->request->post())) {
            $tallasSeleccionadas = Yii::$app->request->post('seleccion_tallas');

            // Iniciamos la transacción
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!empty($tallasSeleccionadas)) {
                    foreach ($tallasSeleccionadas as $idTalla) {
                        $nuevaFila = new Prendatipo();
                        $nuevaFila->prenda = $model->prenda;
                        $nuevaFila->idtalla = $idTalla;

                        if (!$nuevaFila->save()) {
                            // Si una talla falla, lanzamos una excepción para ir al catch
                            throw new \Exception("Error al guardar la talla: " . json_encode($nuevaFila->getErrors()));
                        }
                    }
                } else {
                    // Opcional: Si es obligatorio elegir al menos una talla
                    throw new \Exception("Debes seleccionar al menos una talla.");
                }

                // Si todo salió bien, confirmamos los cambios en la DB
                $transaction->commit();
                Yii::$app->session->setFlash('success', "Prenda y tallas guardadas correctamente.");
                return $this->redirect(['index']);

            } catch (\Exception $e) {
                // Si algo falló, deshacemos todo lo que se alcanzó a insertar
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        $tallasH = Talla::find()
            ->where(['sexo' => ['HOMBRE', 'NIÑO']]) // Forma más limpia de hacer el OR
            ->orderBy(['talla' => SORT_ASC])        // Nota: SORT_ASC es una constante, no un string
            ->all();
        
        $tallasM = Talla::find()
            ->where(['sexo' => ['MUJER', 'NIÑA']]) // Forma más limpia de hacer el OR
            ->orderBy(['talla' => SORT_ASC])        // Nota: SORT_ASC es una constante, no un string
            ->all();
        return $this->render('create', [
            'model' => $model,
            'tallasH' => $tallasH,
            'tallasM' => $tallasM,
        ]);
    }

    /**
     * Updates an existing Prendatipo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
       return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Prendatipo model.
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
            $this->redirect(["prendatipo/index"]);
        } catch (IntegrityException $e) {
            $this->redirect(["prendatipo/index"]);
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la prenda, tiene registros asociados en otros procesos');
        } catch (\Exception $e) {            
            Yii::$app->getSession()->setFlash('error', 'Error al eliminar la prenda, tiene registros asociados en otros procesos');
            $this->redirect(["prendatipo/index"]);
        }
    }

    /**
     * Finds the Prendatipo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prendatipo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prendatipo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
