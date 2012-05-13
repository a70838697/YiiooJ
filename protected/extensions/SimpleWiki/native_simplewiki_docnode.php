<?php
/*
SimpleWiki module, version 1.0 Beta 3, January 6, 2011
copyright (c) Henrik Bechmann, 2009-2011, Toronto, Canada. All rights reserved. simplewiki.org
licence: BSD
*/
#==========================================================================#
#--------------------------[ DOCUMENT NODE ]-------------------------------#
#==========================================================================#
// the parser creates a document tree (document object model) consisting of these nodes
class Native_SimpleWiki_DocNode 
{
    # A node in the document tree.
	public $children;
	public $parent;
	public $kind;

    public function __construct($kind='', $parent=NULL, $content=NULL)
	{
        $this->children = array();
        $this->kind = $kind;
        $this->parent = $parent;
        if (!is_null($content)) $this->content = $content;
        if (!empty($parent))
            $parent->child_append($this);
	}
	protected function child_append($child)
	{
		$this->children[] = $child;
	}
	public function get_display_list() // for debug
	{
		$array = (array) $this;
		$retarr = array();
		foreach ($array as $property => $value)
		{
			if (($property != 'children') and ($property != 'parent'))
				$retarray[$property] = $value;
		}
		return $retarray;
	}
}

