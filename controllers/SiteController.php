<?php

namespace app\controllers;

use app\models\Product;
use app\models\ProductSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'approve', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
	                [
		                'actions' => ['approve'],
		                'allow' => true,
		                'roles' => ['@'],
		                'matchCallback' => function () {
			                return Yii::$app->user->identity->role_id == 2;
		                }
	                ],
	                [
		                'actions' => ['create', 'update', 'delete'],
		                'allow' => true,
		                'roles' => ['@'],
		                'matchCallback' => function () {
			                return Yii::$app->user->identity->role_id == 1;
		                }
	                ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
	    $searchModel = new ProductSearch();
	    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	
	    return $this->render('index', [
		    'searchModel' => $searchModel,
		    'dataProvider' => $dataProvider,
	    ]);
    }
	
	/**
	 * Creates a new Product model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 * @throws \yii\db\Exception
	 */
	public function actionCreate()
	{
		$model = new Product();
		
		if ($model->load(Yii::$app->request->post())) {
			$transaction = Yii::$app->db->beginTransaction();
			
			try {
				$model->save();
				$transaction->commit();
				
				return $this->redirect(['index']);
			} catch (Exception $e) {
				$transaction->rollBack();
				
				echo '<pre>' . print_r($e, true);
				exit;
			}
		}
		
		return $this->render('create', [
			'model' => $model,
		]);
	}
	
	/**
	 * @param $id
	 *
	 * @return Response
	 * @throws \yii\db\Exception
	 */
	public function actionApprove($id)
	{
		$transaction = Yii::$app->db->beginTransaction();
		
		try {
			$model = $this->findModel($id);
			$model->approve();
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();
			
			echo '<pre>' . print_r($e, true);
			exit;
		}
		
		return $this->goHome();
	}
	
	/**
	 * Updates an existing Product model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param $id
	 *
	 * @return string|Response
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionUpdate($id)
	{
		if (Yii::$app->request->isPost) {
			$transaction = Yii::$app->db->beginTransaction();
			
			try {
				$model = $this->findModel($id);
				$model->modify(Yii::$app->request->post());
				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
				
				echo '<pre>' . print_r($e, true);
				exit;
			}
			
			return $this->goHome();
		} else {
			$model = $this->findModel($id);
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}
	
	/**
	 * Deletes an existing Product model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param $id
	 *
	 * @return Response
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 */
	public function actionDelete($id)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$this->findModel($id)->delete();
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();
			
			echo '<pre>' . print_r($e, true);
			exit;
		}
		
		return $this->goHome();
	}
	
    /**
     * Login action.
     *
	 * @return string|Response
	 * @throws \yii\base\Exception
	 */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	/**
	 * Finds the Product model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Product the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Product::findOne($id)) !== null) {
			return $model;
		}
		
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
