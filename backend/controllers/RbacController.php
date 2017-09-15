<?php

namespace backend\controllers;

use backend\models\AddPermissionForm;
use backend\models\AddRole;

class RbacController extends \yii\web\Controller
{
    public function actionPermissionIndex()
    {
        $permissions = \Yii::$app->authManager->getPermissions();
        return $this->render('indexpermission', ['permissions' => $permissions]);
    }

    public function actionAddPermission()
    {
        $model = new AddPermissionForm();
        $model->scenario = AddPermissionForm::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                $auth->add($permission);
                \Yii::$app->session->setFlash('success', '添加权限成功');
                return $this->redirect(['rbac/permission-index']);

            }
        }
        return $this->render('addpermission', ['model' => $model]);
    }

    public function actionEditPermission($name)
    {
//        var_dump($name);exit;
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        $model = new AddPermissionForm();
        $model->name = $permission->name;
        $model->description = $permission->description;
        $model->oldname=$permission->name;
        $model->scenario=AddPermissionForm::SCENARIO_EDIT;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $permission->description = $model->description;
                $auth->update($model->name, $permission);
                \Yii::$app->session->setFlash('success', '修改权限成功');
                return $this->redirect(['rbac/permission-index']);
            }
        }
        return $this->render('addpermission', ['model' => $model]);
    }

    public function actionDeletePermission($name)
    {
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
//        var_dump($permission);exit;
        $auth->remove($permission);
        \Yii::$app->session->setFlash('success', '删除权限成功');
        return $this->redirect(['rbac/permission-index']);
    }

    public function actionAddRole()
    {
        $model = new AddRole();
        $model->scenario = AddRole::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                $auth->add($role);
                if($model->permissions!=null){
                    foreach($model->permissions as $permission){
                        $auth->addChild($role,$auth->getPermission($permission));
                    }
                }
                \Yii::$app->session->setFlash('success', '角色添加成功');
                return $this->redirect(['rbac/index-role']);

            }
        }
        return $this->render('addrole', ['model' => $model]);
    }

    public function actionIndexRole()
    {

        $roles = \Yii::$app->authManager->getRoles();
        return $this->render('roleindex', ['roles' => $roles]);
    }

    public function actionEditRole($name)
    {
        $role = \Yii::$app->authManager->getRole($name);
        $model = new AddRole();
        $model->name = $role->name;
        $model->oldname = $role->name;
        $model->description = $role->description;
        $model->scenario = AddRole::SCENARIO_EDIT;
        $children=AddRole::getChildrenByRole($name);
        $model->permissions=$children;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $role->description = $model->description;
                $role->name = $model->name;
                \Yii::$app->authManager->update($model->oldname, $role);
                if($model->permissions!=null){
                    $auth=\Yii::$app->authManager;
                    $auth->removeChildren($auth->getRole($model->name));
                    foreach($model->permissions as $permission){
                        $auth->addChild($role,$auth->getPermission($permission));
                    }
                }
                \Yii::$app->session->setFlash('success', '修改角色成功');
                return $this->redirect(['rbac/index-role']);
            }
        }


        return $this->render('addrole',['model'=>$model]);
    }
    public function actionDeleteRole($name){
        $auth=\Yii::$app->authManager;
        $role=$auth->getRole($name);
        $auth->remove($role);
        \Yii::$app->session->setFlash('success','删除角色成功');
        return $this->redirect(['rbac/index-role']);
    }
}
