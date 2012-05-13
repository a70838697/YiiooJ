<?php
/** common API to allow insertion of custom extensions.
	For custom extensions, substitute:
	@verbatim
	SimpleWiki_DocNode extends My_SimpleWiki_DocNode\n
	My_SimpleWiki_DocNode extends Muster_SimpleWiki_DocNode @endverbatim
	... which preserves the common $wiki = new SimpleWiki_DocNode() call.
*/
class SimpleWiki_DocNode extends Muster_SimpleWiki_DocNode {}