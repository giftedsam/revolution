<?php
/**
 * Update a plugin.
 *
 * @param integer $id The ID of the plugin.
 * @param string $name The name of the plugin.
 * @param string $plugincode The code of the plugin.
 * @param string $description (optional) A description of the plugin.
 * @param integer $category (optional) The category for the plugin. Defaults to
 * no category.
 * @param boolean $locked (optional) If true, can only be accessed by
 * administrators. Defaults to false.
 * @param boolean $disabled (optional) If true, the plugin does not execute.
 * @param json $events (optional) A json array of system events to associate
 * this plugin with.
 * @param json $propdata (optional) A json array of properties
 *
 * @package modx
 * @subpackage processors.element.plugin
 */
$modx->lexicon->load('plugin','category');

if (!$modx->hasPermission('save_plugin')) return $modx->error->failure($modx->lexicon('permission_denied'));

$plugin = $modx->getObject('modPlugin',$_REQUEST['id']);
if ($plugin == null) return $modx->error->failure($modx->lexicon('plugin_err_not_found'));

if ($plugin->get('locked') && $modx->hasPermission('edit_locked') == false) {
    return $modx->error->failure($modx->lexicon('plugin_err_locked'));
}

/* Validation and data escaping */
if ($_POST['name'] == '') $modx->error->addField('name',$modx->lexicon('plugin_err_not_specified_name'));

$name_exists = $modx->getObject('modPlugin',array(
    'id:!=' => $plugin->get('id'),
    'name' => $_POST['name']
));

if ($name_exists != null) $modx->error->addField('name',$modx->lexicon('plugin_err_exists_name'));

if ($modx->error->hasError()) return $modx->error->failure();

/* category */
if (is_numeric($_POST['category'])) {
    $category = $modx->getObject('modCategory',array('id' => $_POST['category']));
} else {
    $category = $modx->getObject('modCategory',array('category' => $_POST['category']));
}
if ($category == null) {
    $category = $modx->newObject('modCategory');
    if ($_POST['category'] == '' || $_POST['category'] == 'null') {
        $category->set('id',0);
    } else {
        $category->set('category',$_POST['category']);
        if ($category->save() == false) {
            return $modx->error->failure($modx->lexicon('category_err_save'));
        }
    }
}

/* invoke OnBeforeTempFormSave event */
$modx->invokeEvent('OnBeforePluginFormSave',array(
    'mode' => 'new',
    'id' => $plugin->get('id')
));

$plugin->fromArray($_POST);
$plugin->set('locked', isset($_POST['locked']));
$plugin->set('category',$category->get('id'));
$plugin->set('disabled',isset($_POST['disabled']));
$properties = null;
if (isset($_POST['propdata'])) {
    $properties = $_POST['propdata'];
    $properties = $modx->fromJSON($properties);
}
if (is_array($properties)) $plugin->setProperties($properties);

if ($plugin->save() == false) {
    return $modx->error->failure($modx->lexicon('plugin_err_save'));
}

/* change system events */
if (isset($_POST['events'])) {
    $_EVENTS = $modx->fromJSON($_POST['events']);
    foreach ($_EVENTS as $id => $event) {
        if ($event['enabled']) {
            $pe = $modx->getObject('modPluginEvent',array(
                'pluginid' => $plugin->get('id'),
                'evtid' => $event['id'],
            ));
            if ($pe == null) {
                $pe = $modx->newObject('modPluginEvent');
            }
            $pe->set('pluginid',$plugin->get('id'));
            $pe->set('evtid',$event['id']);
            $pe->set('priority',$event['priority']);
            $pe->set('propertyset',$event['propertyset']);
            $pe->save();
        } else {
            $pe = $modx->getObject('modPluginEvent',array(
                'pluginid' => $plugin->get('id'),
                'evtid' => $event['id'],
            ));
            if ($pe == null) continue;
            $pe->remove();
        }
    }
}

/* invoke OnPluginFormSave event */
$modx->invokeEvent('OnPluginFormSave',array(
    'mode' => 'new',
    'id' => $plugin->get('id'),
));

/* log manager action */
$modx->logManagerAction('plugin_update','modPlugin',$plugin->get('id'));

/* empty cache */
$cacheManager= $modx->getCacheManager();
$cacheManager->clearCache();

return $modx->error->success('', $plugin->get(array('id', 'name')));