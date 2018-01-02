<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

class UserModel extends User implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /*public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // 是新纪录 - 创建用户相关表
                //$this->nickname = '新纪录';
                //$this->updated_at = time();
            } else {
                // 不是新纪录
                //$this->nickname = '不是新纪录';
            }
            return true;
        } else {
            return false;
        }
        if ($insert) {
            $user_id = $this->user_id;
            $login_time = time();
            $login_ip = isset(Yii::$app->request->userIP) ? Yii::$app->request->userIP : '127.0.0.1';
            $login_log = new UserLoginLog();
            $login_log->user_id = $user_id;
            $login_log->login_count += 1;
            $login_log->prev_login_time = $login_time;
            $login_log->prev_login_ip = $login_ip;
            $login_log->last_login_time = $login_time;
            $login_log->last_login_ip = $login_ip;
            $login_log->save(false);
        }else{
            $login_log = UserLoginLog::findOne($this->user_id);
            $login_log->login_count += 1;
            $login_log->prev_login_time = $login_log->last_login_time;
            $login_log->prev_login_ip = $login_log->last_login_ip;
            $login_log->last_login_time = time();
            $login_log->last_login_ip = Yii::$app->getRequest()->getUserIP() ?: '127.0.0.1';
            $login_log->login_type = 'pc';
            $login_log->save(false);
        }
    }*/

    /*public function afterSave($insert, $changedAttributes)
    {
        if (parent::beforeSave($insert)){
            if ($insert) {
                static::initUserLoginLog($this->user_id);
            }
        }
    }*/

    // 初始化用户登录日志
    /*public static function initUserLoginLog($user_id)
    {
        $login_time = time();
        $login_ip = Yii::$app->request->getUserIP() ?: '127.0.0.1';

        $model = new UserLoginLog();
        $model->user_id = $user_id;
        $model->login_type = 'pc';
        $model->login_count += 1;
        $model->prev_login_time = $login_time;
        $model->prev_login_ip = $login_ip;
        $model->last_login_time = $login_time;
        $model->last_login_ip = $login_ip;
        $model->status = 1;
        $model->save(false);
    }*/

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByMobilePhone($username)
    {
        return static::findOne(['mobile' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}