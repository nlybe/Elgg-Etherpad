<?php

/**
 * Elgg Etherpad lite plugin
 * @package etherpad
 */
$english = array(
    
    /**
     * Menu items and titles
     */
    'etherpad' => "Pads",
    'etherpad:owner' => "%s's pads",
    'etherpad:friends' => "Friends' pads",
    'etherpad:all' => "All site pads",
    'etherpad:add' => "Add a pad",
    'etherpad:timeslider' => 'History',
    'etherpad:fullscreen' => 'Fullscreen',
    'etherpad:none' => 'No pads created yet',
    'etherpad:group' => 'Group pads',
    'groups:enablepads' => 'Enable group pads',
    
    /**
     * River
     */
    'river:create:object:etherpad' => '%s created a new collaborative pad %s',
    'river:create:object:subpad' => '%s created a new collaborative pad %s',
    'river:update:object:etherpad' => '%s updated the collaborative pad %s',
    'river:update:object:subpad' => '%s updated the collaborative pad %s',
    'river:comment:object:etherpad' => '%s commented on the collaborative pad %s',
    'river:comment:object:subpad' => '%s commented on the collaborative pad %s',
    'item:object:etherpad' => 'Pads',
    'item:object:subpad' => 'Subpads',
    
    /**
     * Status messages
     */
    'etherpad:saved' => "Your pad was successfully saved.",
    'etherpad:error:notsaved' => "Error while saving pad.",
    'etherpad:delete:success' => "Your pad was successfully deleted.",
    'etherpad:delete:failure' => "Your pad could not be deleted. Please try again.",
    'etherpad:server:failure' => "Sorry, pads are not available right now.",
    
    
    /**
     * Edit page
     */
    'etherpad:title' => "Title",
    'etherpad:tags' => "Tags",
    'etherpad:access_id' => "Read access",
    'etherpad:write_access_id' => "Write access",
    'etherpad:form:enable_iframe' => "URL for iframe",
    'etherpad:form:enable_iframe:help' => 'If want to use an external pad, use the shared URL provided by this pad. See <a href="%s" target="_blank">this image</a> how to share a pad.<br />Leave blank for creating a new pad.',
    'etherpad:error:invalid_url' => 'Invalid iframe URL',
    
    /**
     * Admin settings
     */
    'etherpad:etherpadhost' => "Etherpad lite host address:",
    'etherpad:etherpadkey' => "Etherpad lite api key:",
    'etherpad:showchat' => "Show chat?",
    'etherpad:linenumbers' => "Show line numbers?",
    'etherpad:showcontrols' => "Show controls?",
    'etherpad:monospace' => "Use monospace font?",
    'etherpad:showcomments' => "Show comments?",
    'etherpad:newpadtext' => "New pad text:",
    'etherpad:pad:message' => 'New pad created successfully.',
    'etherpad:integrateinpages' => "Integrate pads and pages? (Requires Pages plugin to be enabled)",
    'etherpad:settings:iframe' => 'Use External Pad',
    'etherpad:settings:enable_iframe' => 'Enable Pad with iframe',
    'etherpad:settings:enable_iframe:help' => 'If select yes, the user will be able to use an external pad by inserting the shared linked offered from Etherpad. See <a href="%s" target="_blank">this image</a> how to share a pad.',
    'etherpad:settings:iframe_expire' => 'Pad Expires',
    'etherpad:settings:iframe_expire:help' => 'If external pad expires after last edit, enter the number of expiration days e.g. 30',
    
    
    /**
     * Widget
     */
    'etherpad:profile:numbertodisplay' => "Number of pads to display",
    'etherpad:profile:widgetdesc' => "Display your latest pads",
    
    /**
     * Sidebar items
     */
    'etherpad:newchild' => "Create a sub-pad",
    
    /**
     * General
     */
    'etherpad:iframe:note' => "This pad will be DELETED %s days after the last edit! ",
       
    
);

add_translation('en', $english);
