<?php

/**
 * Handles user accounts
 * 
 * @author Sam Stenvall <neggelandia@gmail.com>
 * @author Geoffrey Bonneville <geoffrey.bonneville@gmail.com>
 * @copyright Copyright &copy; Sam Stenvall 2013-
 * @license https://www.gnu.org/licenses/gpl.html The GNU General Public License v3.0
 */
class UserController extends AdminOnlyController
{
	
	/**
	 * Override the admin only access rules to allow ordinary users to change 
	 * their passwords
	 * @return the access rules for this controller
	 */
	public function accessRules()
	{
		return array_merge(
			array(
				array('allow',
					'actions'=>array('changePassword'),
				),
			), parent::accessRules()
		);
	}

	/**
	 * Updates a password
	 */
	public function actionChangePassword()
	{
		$model = new ChangePasswordForm();

		if (isset($_POST['ChangePasswordForm']))
		{
			$model->attributes = $_POST['ChangePasswordForm'];

			if ($model->validate())
			{
				// Change the password
				$user = $this->loadModel(Yii::app()->user->id);
				$user->password = $model->newPassword;
				$user->save();

				// Log and inform
				$this->log('"%s" updated his/her password', Yii::app()->user->name);
				Yii::app()->user->setFlash('success', Yii::t('User', 'Password successfully changed'));

				$this->redirect(array('movie/index'));
			}
		}

		$this->render('changePassword', array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new user
	 */
	public function actionCreate()
	{
		$model = new User();

		if ($this->saveFromPost($model))
		{
			$this->log('"%s" created user "%s"', Yii::app()->user->name, 
					$model->username);
			
			Yii::app()->user->setFlash('success', Yii::t('User', 'Created user {username}', 
					array('{username}'=>'<em>'.$model->username.'</em>')));

			$this->redirect(array('admin'));
		}

		$this->render('create', array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a user
	 * @param int $id the user ID
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		
		// Clear the password
		$model->password = '';

		if ($this->saveFromPost($model))
		{
			$this->log('"%s" updated user "%s"', Yii::app()->user->name, 
					$model->username);
			
			Yii::app()->user->setFlash('success', Yii::t('User', 'Updated user {username}', 
					array('{username}'=>'<em>'.$model->username.'</em>')));

			$this->redirect(array('admin'));
		}

		$this->render('update', array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a user
	 * @param int $id the user ID
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);
		$model->delete();
		
		$this->log('"%s" deleted user "%s"', Yii::app()->user->name, 
						$model->username);

		$this->redirectOnDelete();
	}

}