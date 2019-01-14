<?php

namespace backend\controllers;

use Yii;
use common\models\Adminuser;
 
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\AdminuserSearch;
use backend\models\SignupForm;
use backend\models\ResetpwdForm;
use common\models\AuthItem;
use common\models\AuthAssignment;

/**
 * AdminuserController implements the CRUD actions for Adminuser model.
 */
class AdminuserController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all Adminuser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminuserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Adminuser model.
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
     * Creates a new Adminuser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) ) {
            if($user = $model->signup()){
                //添加用户成功
                
                return $this->redirect(['view', 'id' => $user->id]);
            }
           
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionResetpwd($id)
    {
        $model = new ResetpwdForm();
        
        if ($model->load(Yii::$app->request->post()) ) {
            if( $model->resetPassword($id)){
                //添加用户成功
                
                return $this->redirect(['index']);
            }
            
        }
        
        return $this->render('resetpwd', [
            'model' => $model,
        ]);
    }
    
    /**
     * Updates an existing Adminuser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Adminuser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Adminuser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Adminuser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Adminuser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionPrivilege($id){
        //step1.找出所有角色，提供给多选按钮 checkboxlist
        $allPrivileges = AuthItem::find()->select(['name','description'])
                        ->where(['type'=>1])->orderBy('description')->all();
                        foreach ($allPrivileges as $pri){
                            $allPrivilegesArray[$pri->name] = $pri->description;
                        }
         //step2.当前用户的拥有的角色
         $authAssignments = AuthAssignment::find()->select(['item_name'])
         ->where(['user_id'=>$id])->all();
         
         $authAssignmentsArray = array();
         foreach ($authAssignments as $authAssignment){
             array_push($authAssignmentsArray, $authAssignment->item_name);
         }
         //step 3. 从表单提交的数据，来更新AuthAssignment表，从而用户的角色发生变化
         if(isset($_POST['newPri'])){
             AuthAssignment::deleteAll('user_id=:id',[':id'=>$id]);
             
             $newPri = $_POST['newPri'];
             $arrlength = count($newPri);
             
             for ($i = 0; $i < $arrlength; $i++) {
                 $aPri = new AuthAssignment();
                 $aPri->item_name = $newPri[$i];
                 $aPri->user_id = $id;
                 $aPri->created_at = time();
                 $aPri->save();
             }
             return $this->redirect(['index']);
             
         }
         
        //step4.渲染checkboxlist表单
        return $this->render('privilege',['id'=>$id,'authAssignmentArray'=>$authAssignmentsArray,
            'allPrivilegesArray'=>$allPrivilegesArray
        ]);
        
    }
    
}
