<?php
/** common API to allow insertion of custom extensions.
	For custom extensions, substitute:
	@verbatim
	SimpleWiki extends My_SimpleWiki\n
	My_SimpleWiki extends Muster_SimpleWiki @endverbatim
	... which preserves the common $wiki = new SimpleWiki() call.
*/
class SimpleWiki extends Muster_SimpleWiki {}