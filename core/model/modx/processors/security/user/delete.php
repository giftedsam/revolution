<?php
/**
 * Deletes a user
 *
 * @param integer $id The ID of the user
 *
 * @package modx
 * @subpackage processors.security.user
 */
$modx->lexicon->load('user');

if (!$modx->hasPermission(array('access_permissions' => true, 'delete_user' => true))) return $modx->error->failure($modx->lexicon('permission_denied'));

$user = $modx->getObject('modUser',$_REQUEST['id']);
if ($user == null) return $modx->error->failure($modx->lexicon('user_err_nf'));


/* check if we are deleting our own record */
if ($user->get('id') == $modx->user->get('id')) {
	return $modx->error->failure($modx->lexicon('user_err_cannot_delete_self'));
}

/* invoke OnBeforeUserFormDelete event */
$modx->invokeEvent('OnBeforeUserFormDelete',array(
	'id' => $user->get('id'),
));

/* remove user */
if ($user->remove() == false) {
    return $modx->error->failure($modx->lexicon('user_err_remove'));
}

/* invoke OnManagerDeleteUser event */
$modx->invokeEvent('OnManagerDeleteUser',array(
	'userid'	=> $user->get('id'),
	'username'	=> $user->get('username'),
));

/* invoke OnUserFormDelete event */
$modx->invokeEvent('OnUserFormDelete',array('id' => $user->get('id')));

/* log manager action */
$modx->logManagerAction('user_delete','modUser',$user->get('id'));

return $modx->error->success();