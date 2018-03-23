<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean']
        ];
    }

    /**
     * Logs in a user using the provided username and password.
	 * @return bool
	 * @throws \yii\base\Exception
	 */
    public function login()
    {
	    if ($this->validate()) {
		    $user = User::findByUsername($this->username);
		    
		    if (!$user || !$user->validatePassword($this->password)) {
			    $this->addError('password', Yii::t('app', 'Incorrect username or password'));
			    return false;
		    }
		    
		    return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
	    }
	
	    return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
