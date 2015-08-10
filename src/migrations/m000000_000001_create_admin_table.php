<?php

use nullref\admin\models\Admin;
use yii\db\Schema;
use yii\db\Migration;
use yii\rbac\BaseManager;

class m000000_000001_create_admin_table extends Migration
{
    protected $tableName = '{{%admin}}';


    public function up()
    {
        $tableOptions = null;
        if (\Yii::$app->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        /**
         * Create table
         */
        $this->createTable($this->tableName, [
            'id' => Schema::TYPE_PK,
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'firstName' => Schema::TYPE_STRING . ' NULL',
            'lastName' => Schema::TYPE_STRING . ' NULL',
            'role' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'passwordHash' => Schema::TYPE_STRING . ' NOT NULL',
            'passwordResetToken' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'passwordResetExpire' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'createdAt' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updatedAt' => Schema::TYPE_INTEGER . ' NOT NULL',
            'authKey' => Schema::TYPE_STRING . '(32) NULL DEFAULT NULL',
            'emailConfirmToken' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'data' => Schema::TYPE_TEXT,
        ], $tableOptions);

        /**
         * Default admin values
         */
        $data = [
            'email' => 'admin@test.com',
            'passwordHash' => \Yii::$app->security->generatePasswordHash('password'),
            'firstName' => 'Admin',
            'lastName' => 'Admin',
            'createdAt' => time(),
            'updatedAt' => time(),
            'status' => Admin::STATUS_ACTIVE,
        ];

        /**
         * Create default admin
         */
        $this->db->createCommand()->insert($this->tableName, $data)->execute();

        /** @var BaseManager $authManager */
        $authManager = \Yii::$app->getModule('admin')->get('authManager', false);

        if (($authManager !== null) && ($role = $authManager->getRole('admin')) !== null) {
            $id = $this->db->getLastInsertID();
            $authManager->assign($role, $id);
        };
    }

    public function down()
    {
        $this->dropTable($this->tableName);
        return true;
    }

}