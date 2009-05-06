<?php
/**
 * Creates a context setting
 *
 * @param string $context_key/$fk The key of the context
 * @param string $key The key of the setting
 * @param string $value The value of the setting.
 * @param string $xtype (optional) The rendering type for the setting. Defaults
 * to textfield.
 * @param string $namespace (optional) The namespace of the setting. Defaults to
 * core.
 * @param string $area (optional) The area of the setting. Defaults to a blank
 * area.
 *
 * @package modx
 * @subpackage processors.context.setting
 */
$modx->lexicon->load('setting');

if (!$modx->hasPermission('settings')) return $modx->error->failure($modx->lexicon('permission_denied'));

$_POST['context_key'] = isset($_POST['fk']) ? $_POST['fk'] : 0;
if (!$context = $modx->getObject('modContext', $_POST['context_key'])) return $modx->error->failure($modx->lexicon('setting_err_nf'));
if (!$context->checkPolicy('save')) return $modx->error->failure($modx->lexicon('permission_denied'));

$ae = $modx->getObject('modContextSetting',array(
    'key' => $_POST['key'],
    'context_key' => $_POST['context_key'],
));
if ($ae != null) return $modx->error->failure($modx->lexicon('setting_err_ae'));

$setting= $modx->newObject('modContextSetting');
$setting->fromArray($_POST,'',true);

/* set lexicon name/description */
$topic = $modx->getObject('modLexiconTopic',array(
    'name' => 'default',
    'namespace' => $setting->namespace,
));
if ($topic == null) {
    $topic = $modx->newObject('modLexiconTopic');
    $topic->set('name','default');
    $topic->set('namespace',$setting->namespace);
    $topic->save();
}


$entry = $modx->getObject('modLexiconEntry',array(
    'namespace' => $setting->get('namespace'),
    'name' => 'setting_'.$_POST['key'],
));
if ($entry == null) {
    $entry = $modx->newObject('modLexiconEntry');
    $entry->set('namespace',$setting->get('namespace'));
    $entry->set('name','setting_'.$_POST['key']);
    $entry->set('topic',$topic->get('id'));
}
$entry->set('value',$_POST['name']);
$entry->save();
$entry->clearCache();

$description = $modx->getObject('modLexiconEntry',array(
    'namespace' => $setting->get('namespace'),
    'name' => 'setting_'.$_POST['key'].'_desc',
));
if ($description == null) {
    $description = $modx->newObject('modLexiconEntry');
    $description->set('namespace',$setting->get('namespace'));
    $description->set('name','setting_'.$_POST['key'].'_desc');
    $description->set('topic',$topic->get('id'));
}

$description->set('value',$_POST['description']);
$description->save();
$description->clearCache();


if ($setting->save() === false) {
    $modx->error->checkValidation($setting);
    return $modx->error->failure($modx->lexicon('setting_err_save'));
}

$modx->reloadConfig();

return $modx->error->success();