<?php
/**
 * Elgg Etherpad lite plugin
 * @package etherpad
 */

$plugin = elgg_get_plugin_from_id('etherpad');

// set default values
if (!isset($vars['entity']->etherpad_host)) {
    $vars['entity']->etherpad_host = "http://beta.etherpad.org";
}
if (!isset($vars['entity']->etherpad_key)) {
    $vars['entity']->etherpad_key = 'EtherpadFTW';
}
if (!isset($vars['entity']->integrate_in_pages)) {
    $vars['entity']->integrate_in_pages = 'no';
}
if (!isset($vars['entity']->show_chat)) {
    $vars['entity']->show_chat = 'no';
}

if (!isset($vars['entity']->line_numbers)) {
    $vars['entity']->line_numbers = 'no';
}

if (!isset($vars['entity']->monospace_font)) {
    $vars['entity']->monospace_font = 'no';
}

if (!isset($vars['entity']->show_controls)) {
    $vars['entity']->show_controls = 'yes';
}

if (!isset($vars['entity']->show_comments)) {
    $vars['entity']->show_comments = 'yes';
}

if (!isset($vars['entity']->new_pad_text)) {
    $vars['entity']->new_pad_text = elgg_echo('etherpad:pad:message');
}

$options_yn = array(
    ETHERPAD_YES => elgg_echo('option:yes'),
    ETHERPAD_NO => elgg_echo('option:no'),
); 

?>
<div>
    <br /><label><?php echo elgg_echo('etherpad:etherpadhost'); ?></label><br />
<?php echo elgg_view('input/text', array('name' => 'params[etherpad_host]', 'value' => $vars['entity']->etherpad_host, 'class' => 'text_input',)); ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:etherpadkey'); ?></label><br />
<?php echo elgg_view('input/text', array('name' => 'params[etherpad_key]', 'value' => $vars['entity']->etherpad_key, 'class' => 'text_input',)); ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:newpadtext'); ?></label><br />
<?php echo elgg_view('input/text', array('name' => 'params[new_pad_text]', 'value' => $vars['entity']->new_pad_text, 'class' => 'text_input',)); ?>
</div>

<div>
    <br /><label><?php echo elgg_echo('etherpad:integrateinpages'); ?></label><br />
<?php
echo elgg_view('input/dropdown', array(
    'name' => 'params[integrate_in_pages]',
    'options_values' => $options_yn,
    'value' => $vars['entity']->integrate_in_pages,
));
?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:showcontrols'); ?></label><br />
    <?php
    echo elgg_view('input/dropdown', array(
        'name' => 'params[show_controls]',
        'options_values' => $options_yn,
        'value' => $vars['entity']->show_controls,
    ));
    ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:showchat'); ?></label><br />
    <?php
    echo elgg_view('input/dropdown', array(
        'name' => 'params[show_chat]',
        'options_values' => $options_yn,
        'value' => $vars['entity']->show_chat,
    ));
    ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:linenumbers'); ?></label><br />
    <?php
    echo elgg_view('input/dropdown', array(
        'name' => 'params[line_numbers]',
        'options_values' => $options_yn,
        'value' => $vars['entity']->line_numbers,
    ));
    ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:monospace'); ?></label><br />
    <?php
    echo elgg_view('input/dropdown', array(
        'name' => 'params[monospace_font]',
        'options_values' => $options_yn,
        'value' => $vars['entity']->monospace_font,
    ));
    ?>
</div>

<div>
    <label><?php echo elgg_echo('etherpad:showcomments'); ?></label><br />
    <?php
    echo elgg_view('input/dropdown', array(
        'name' => 'params[show_comments]',
        'options_values' => $options_yn,
        'value' => $vars['entity']->show_comments,
    ));
    ?>
</div>


<?php
$iframe_settings .= elgg_format_element('div', [], elgg_view_input('dropdown', array(
    'name' => 'params[enable_iframe]',
    'value' => ($plugin->enable_iframe?$plugin->enable_iframe:ETHERPAD_NO),
    'options_values' => $options_yn,
    'label' => elgg_echo('etherpad:settings:enable_iframe'),
    'help' => elgg_echo('etherpad:settings:enable_iframe:help', array(elgg_get_simplecache_url('etherpad/images/iframe_sample.png'))),
    'required' => false,
)));

$iframe_settings .= elgg_format_element('div', [], elgg_view_input('text', array(
    'name' => 'params[iframe_expire]',
    'value' => ($plugin->iframe_expire?$plugin->iframe_expire:0),
    'label' => elgg_echo('etherpad:settings:iframe_expire'),
    'help' => elgg_echo('etherpad:settings:iframe_expire:help'),
    'required' => false,
    'style' => 'width:50px;',
)));

$title = elgg_format_element('h3', [], elgg_echo('etherpad:settings:iframe'));
echo elgg_view_module('inline', '', $iframe_settings, ['header' => $title]);

?>
