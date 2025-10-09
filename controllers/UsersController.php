<?php

namespace app\controllers;

use app\models\forms\EditProfileForm;
use app\models\forms\SecureProfileForm;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UsersController extends AuthorizedController
{
    /**
     * Просмотр профиля пользователя.
     *
     * @param int $id Идентификатор пользователя.
     * @return string Рендер страницы пользователя.
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException("Пользователя с id $id не найдено");
        }
        if ($user->role !== User::ROLE_EXECUTOR) {
            throw new NotFoundHttpException('Нет прав на просмотр данной страницы');
        }

        return $this->render('view', compact('user'));
    }

    /**
     * Редактирование профиля пользователя.
     *
     * @return string|Response Страница редактирования профиля или обновление страницы.
     * @throws Exception
     */
    public function actionEdit(): string|Response
    {
        $profileForm = new EditProfileForm();
        $user = User::findOne(Yii::$app->user->getId());

        if (!Yii::$app->request->isPost) {
            return $this->render('edit', [
                'user' => $user,
                'profile' => $profileForm,
            ]);
        }
        $post = Yii::$app->request->post();

        $specializations = $post['EditProfileForm']['specializations'] ?? null;
        if ($user->role === User::ROLE_EXECUTOR && is_string($specializations)) {
            $post['EditProfileForm']['specializations'] = explode(',', $specializations);
        }

        if ($profileForm->load($post) && $profileForm->validate()) {
            $profileForm->saveProfile($user->id);
            return $this->refresh();
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
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionSecure(): string|Response
    {
        $secureForm = new SecureProfileForm();
        $user = User::findOne(Yii::$app->user->getId());

        $secureForm->hiddenContacts = (bool)$user->hidden_contacts;

        if (!Yii::$app->request->isPost) {
            return $this->render('secure', [
                'user' => $user,
                'secure' => $secureForm,
            ]);
        }

        $post = Yii::$app->request->post();
        $secureForm->load($post);
        if ($secureForm->load($post) && $secureForm->validate()) {
            $secureForm->saveProfile($user->id);
            return $this->refresh();
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