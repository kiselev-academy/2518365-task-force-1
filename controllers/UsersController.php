<?php

namespace app\controllers;

use app\models\Category;
use app\models\forms\EditProfileForm;
use app\models\forms\SecureProfileForm;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UsersController extends AuthorizedController
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException("Пользователя с ID $id не найдено");
        }
        if ($user->role !== User::ROLE_EXECUTOR) {
            throw new NotFoundHttpException("Этот пользователь не является исполнителем, вы не можете просматривать его профиль!");
        }
        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');
        return $this->render('view', compact('user', 'categories'));
    }

    /**
     * Редактирование профиля пользователя.
     *
     * @return string|Response Страница редактирования профиля или обновление страницы.
     * @throws Exception
     */
    public function actionEdit(): string |Response
    {
        $profileForm = new EditProfileForm();
        $user = User::getCurrentUser();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            if ($user->role === User::ROLE_EXECUTOR) {
                $specializations = $post['EditProfileForm']['specializations'];
                if (isset($specializations) && is_string($specializations)) {
                    $post['EditProfileForm']['specializations'] = explode(',', $specializations);
                }
            }

            if ($profileForm->load($post) && $profileForm->validate()) {
                $profileForm->saveProfile($user->id);
                return $this->refresh();
            }
        }

        return $this->render('edit', [
            'user' => $user,
            'profile' => $profileForm,
        ]);
    }

    /**
     * Изменение настроек безопасности профиля пользователя.
     *
     * @return string|Response Страница безопасности или обновление страницы.
     */
    public function actionSecure(): string |Response
    {
        $secureForm = new SecureProfileForm();
        $user = User::getCurrentUser();

        $secureForm->hiddenContacts = (bool) $user->hidden_contacts;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $secureForm->load($post);
            if ($secureForm->load($post) && $secureForm->validate()) {
                $secureForm->saveProfile($user->id);
                return $this->refresh();
            }
        }

        return $this->render('secure', [
            'user' => $user,
            'secure' => $secureForm,
        ]);
    }

    /**
     * Выход из аккаунта пользователя.
     *
     * @return Response Редирект на главную страницу.
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}