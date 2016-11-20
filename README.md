Etherpad for Elgg
=================

[Etherpad] (http://etherpad.org/) integration on Elgg.

Etherpad is a highly customizable Open Source online editor providing collaborative editing in really real-time.

## Requires
- [Elgg 2.0](http://elgg.org/download.php/)
- [Etherpad lite 1.2](http://etherpad.org/download/)

## Acknowledgements	
- Upgrade to v2.2 was funded by Andre Bouchain.

## Install Etherpad Lite:
- Follow INSTALL instructions.
- You will install ep-lite dependences (node.js, npm...), install and configure ep-lite and finally create a daemon to manage it.

## Install Plugin:
- Extract plugin. 
- Copy the etherpad folder and its contents into elgg's mod directory.
- Enable plugin from admin settings.

## Configure:
- Etherpad Lite host address (the address you use to connect to your etherpad service) If you have Etherpad Lite installed locally the address will be something like http://127.0.0.1:9001 (localhost, port 9001)
- Etherpad Lite API key, this is your unique key used to interact with the etherpad api, sort of like a password. The plugin needs this to function correctly, you can find your api key in etherpad lites root directory in a text file called APIKEY.txt
- New pad text, this is the text that will be added to each new pad.
- Integrate pads and pages, Removes normal pad behavior and integrates pads into pages. (Requires pages plugin)  
- Show controls, show/hide etherpad lites control bar.
- Show chat, show/hide etherpad lites built in chat service.
- Show line numbers, show/hide line numbers.
- Use monospace font.
- Show comments, show/hide elgg comments on etherpads.
- Supported languages: English, Spanish.
	
## Notes: 
- Huge thanks to Sem for this release, really nice work.  
- Etherpad Lite is not included in the plugin, you need to install and configure it separately.

## Changes:
- See CHANGELOG file 
	
## License:
- GPL version 2+, see LICENSE for more details.







