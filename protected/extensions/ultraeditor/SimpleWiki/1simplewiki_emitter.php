<?php
/** common API to allow insertion of custom extensions.
	For custom extensions, substitute:
	@verbatim
	SimpleWiki_Emitter extends My_SimpleWiki_Emitter\n
	My_SimpleWiki_Emitter extends Muster_SimpleWiki_Emitter @endverbatim
	... which preserves the common $wiki = new SimpleWiki_Emitter() call.
*/
class SimpleWiki_Emitter extends Muster_SimpleWiki_Emitter {}