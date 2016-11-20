<?php
/**
 * Elgg Etherpad lite plugin
 * @package etherpad
 */

$variables = elgg_get_config('etherpad');

foreach ($variables as $name => $type) {
    if ($name == 'parent_guid')
        continue;
    
    if ($name == 'iframe_url') {
        if (isIframeEnabled()) {
            echo elgg_format_element('div', [], elgg_view_input('text', array(
                'name' => 'iframe_url',
                'value' => $vars['iframe_url'],
                'label' => elgg_echo('etherpad:form:enable_iframe'),
                'help' => elgg_echo('etherpad:form:enable_iframe:help', array(elgg_get_simplecache_url('etherpad/images/iframe_sample.png'))),
                'required' => false,
            )));    
        }  
        
        continue;
    }
    
    ?>
    <div>
        <label><?php echo elgg_echo("etherpad:$name") ?></label>
        <?php
        if ($type != 'longtext') {
            echo '<br />';
        }
        ?>
        <?php
        echo elgg_view("input/$type", array(
            'name' => $name,
            'value' => $vars[$name],
        ));
        ?>
    </div>
    <?php
}

echo '<div class="elgg-foot">';
if ($vars['guid']) {
    echo elgg_view('input/hidden', array(
        'name' => 'page_guid',
        'value' => $vars['guid'],
    ));
}
echo elgg_view('input/hidden', array(
    'name' => 'container_guid',
    'value' => $vars['container_guid'],
));
if ($vars['parent_guid']) {
    echo elgg_view('input/hidden', array(
        'name' => 'parent_guid',
        'value' => $vars['parent_guid'],
    ));
}

echo elgg_view('input/submit', array('value' => elgg_echo('save')));

echo '</div>';
