<?php
/** common API to allow insertion of custom extensions.
	For custom extensions, substitute:
	@verbatim
	SimpleWiki_Parser extends My_SimpleWiki_Parser\n
	My_SimpleWiki_Parser extends Muster_SimpleWiki_Parser @endverbatim
	... which preserves the common $wiki = new SimpleWiki_Parser() call.
*/
class SimpleWiki_Parser extends Muster_SimpleWiki_Parser {}