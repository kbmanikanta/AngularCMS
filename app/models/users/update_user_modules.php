<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$form_data = $_POST;

	if ($form_data['author'] == 1) // superadmin
	{
		if (!empty($form_data['user']) && !empty($form_data['module']) && !empty($form_data['access'])) 
		{
			$user_id = $form_data['user'];
			$module_id = $form_data['module'];
			$access = $form_data['access'] == 'true' ? 1 : 0;

			$query = ' UPDATE access_users SET access = :access' .
			'          WHERE user_id = :user_id AND module_id = :module_id';

			$statement = $dbc->prepare($query);

			$statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
			$statement->bindParam(':module_id', $module_id, PDO::PARAM_INT);
			$statement->bindParam(':access', $access, PDO::PARAM_INT);

			$statement->execute();

			if ($statement->rowCount())
			{
				$message = 'Rekord został poprawnie zapisany.';
				$success = true;
			}
			else
			{
				$message = 'Rekord nie został zapisany.';
				$success = false;
			}
		}
		else
		{
			$message = 'Brak wymaganych danych.';
			$success = false;
		}
	}
	else // not superadmin
	{
		$message = 'Brak uprawnień do wykonania uruchomionej akcji.';
		$success = false;
	}
}
else
{
	$message = 'Brak uprawnień do wykonania uruchomionej akcji.';
	$success = false;
}

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
