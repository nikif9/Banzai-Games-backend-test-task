<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\News;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\helpers\WordFilterHelper;

class ApiController extends ActiveController
{
    public $modelClass = 'app\models\News';

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'corsFilter' => [
                    'class' => \yii\filters\Cors::class,
                ]
            ]
        );
    }

    protected function verbs() {
        $verbs = parent::verbs();
        $verbs =  [
            'getlist' => ['GET'],
            'create' => ['POST', 'OPTIONS'],
            'update' => ['get', 'POST', 'OPTIONS'],
            'delete' => ['DELETE', 'OPTIONS'],
        ];
        return $verbs;
    } 

    public function actions()
    {
        $actions = parent::actions();
        // Отключаем действия, которые не нужны
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     *  endpoint /api/getlist [get] чтобы получить все новости из бд
     * @return string json с новостями из базы данных с количеством страниц
     */
    public function actionGetlist(){
        $page = empty(Yii::$app->request->get('page') )?  1 : Yii::$app->request->get('page');
        $limit = empty(Yii::$app->request->get('limit')) ? 5 : Yii::$app->request->get('limit');
        $query = News::find();
        $count = $query->count();
        $news = $query->offset(($page - 1 ) * $limit)->limit($limit)->orderBy('id')->all();
        return ['status' => 'success', 'data' => $news, "totalPages" => ceil($count / $limit)];
    }
    /**
     *  endpoint /api/create [post] для создание новости отправка запроса по типу
     * {"title":"title", "description":"description","text":"text" }
     * @return string json с статусом успеха
     */
    public function actionCreate(){
        $model = new News();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->title =  WordFilterHelper::formatText($model->title);
        $model->description = WordFilterHelper::formatText($model->description);
        $model->text = WordFilterHelper::formatText($model->text);
        if ($model->save()) {
            Yii::$app->response->statusCode = 201; // Код 201 Created
            return ['status' => 'success', 'message' => 'News created successfully.'];
        } else {
            Yii::$app->response->statusCode = 422; // Код 422 Unprocessable Entity
            return ['status' => 'error', 'errors' => $model->errors];
        }
    }

    /**
     *  endpoint /api/update/${ID} [post] для обнволение уже созданной новости отправка запроса по типу
     * {"title":"title", "description":"description","text":"text" } и id указывается в url
     * @return string json с статусом успеха
     */
    public function actionUpdate($id){
        $model = News::findOne($id);
        if (!$model) {
            Yii::$app->response->statusCode = 404; // Код 404 Not Found
            return ['status' => 'error', 'message' => 'News not found.'];
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->title =  WordFilterHelper::formatText($model->title);
        $model->description = WordFilterHelper::formatText($model->description);
        $model->text = WordFilterHelper::formatText($model->text);

        if ($model->save()) {
            return ['status' => 'success', 'message' => 'News updated successfully.'];
        } else {
            Yii::$app->response->statusCode = 422; // Код 422 Unprocessable Entity
            return ['status' => 'error', 'errors' => $model->errors];
        }
    }
    /**
     *  endpoint /api/delete/${ID} [delete] для удаление созданной новости id указывается в url
     * @return string json с статусом успеха
     */
    public function actionDelete($id){
        $model = News::findOne($id);

        if (!$model) {
            Yii::$app->response->statusCode = 404; // Код 404 Not Found
            return ['status' => 'error', 'message' => 'News not found.'];
        }

        if ($model->delete()) {
            return ['status' => 'success', 'message' => 'News deleted successfully.'];
        } else {
            Yii::$app->response->statusCode = 500; // Код 500 Internal Server Error
            return ['status' => 'error', 'message' => 'Error deleting news.'];
        }
    }
}
