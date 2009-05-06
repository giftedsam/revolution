<?php
/**
 * Updates a context.
 *
 * @param string $key The key of the context
 * @param json $settings A json array of context settings
 *
 * @package modx
 * @subpackage processors.context
 */
$modx->lexicon->load('context');

if (!$modx->hasPermission('edit_context')) return $modx->error->failure($modx->lexicon('permission_denied'));

if (!isset($_REQUEST['key'])) return $modx->error->failure($modx->lexicon('context_err_ns'));
$context= $modx->getObject('modContext', $_REQUEST['key']);
if ($context == null) return $modx->error->failure($modx->lexicon('context_err_nfs',array('key' => $_REQUEST['key'])));

$context->fromArray($_POST);

if ($context->save() === false) {
	return $modx->error->failure($modx->lexicon('context_err_save'));
}

/* update context settings */
if (isset($_POST['settings'])) {
    $_SETTINGS = $modx->fromJSON($_POST['settings']);
    foreach ($_SETTINGS as $id => $st) {
        $setting = $modx->getObject('modContextSetting',array(
            'context_key' => $context->get('key'),
            'key' => $st['key'],
        ));
        $setting->set('value',$st['value']);

        /* if name changed, change lexicon string */
        $entry = $modx->getObject('modLexiconEntry',array(
            'namespace' => 'core',
            'name' => 'setting_'.$st['key'],
        ));
        if ($entry != null) {
            $entry->set('value',$st['name']);
            $entry->save();
            $entry->clearCache();
        }

        if ($setting->save() == false) {
            return $modx->error->failure($modx->lexicon('setting_err_save'));
        }
    }
}


/* log manager action */
$modx->logManagerAction('context_update','modContext',$context->get('key'));

return $modx->error->success('', $context);