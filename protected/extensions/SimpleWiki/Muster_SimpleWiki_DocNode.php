<?php
/**
@author copyright (c) Henrik Bechmann, 2009-2012, Toronto, Canada. All rights reserved. simplewiki.org\n
Licence: BSD, see licence.txt
@version 1.0
@date February 9, 2012
*/
#==========================================================================#
#--------------------------[ DOCUMENT NODE ]-------------------------------#
#==========================================================================#
/** The parser creates a document tree (document object model) consisting of these nodes
	to debug the contents of a node, use var_dump($node->get_display_list()), which avoids parent and children nodes
*/
class Muster_SimpleWiki_DocNode 
{
/**@{@name Types of nodes */
	/** Document node. The root node.
	No parsed properties
	
	\b Added by node emitter: \b opentag_head = '', \b closetag = '', 
		\b elementcontent set from child emitters
	*/
	const DOCUMENT = 'document';
	
	/** Text node. No html markup.
	
	\b Parsed properties: \b textcontent
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->char, $rules->escape)
	@sa Muster_SimpleWiki_Parser::_char_node (inline non-markup character sets)
	@sa Muster_SimpleWiki_Parser::_escape_node (escaped characters)
	@sa Muster_SimpleWiki_Parser::_span_node (when error)
	@sa Muster_SimpleWiki_Emitter::_text_emit
	*/
	const TEXT = 'text';
	
	/** Paragraph node (\<p>).
	\b Parsed properties: \b decoration
	
	\b Added by node emitter: \b opentag_head = "\n<p", \b opentag_tail = ">", \b closetag = "</p>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->paragraph)
	@sa Muster_SimpleWiki_Parser::_paragraph_node
	@sa Muster_SimpleWiki_Emitter::_paragraph_emit
	*/
	const PARAGRAPH = 'paragraph';
	
	/** Heading node (\<h1-6>).
	\b Parsed properties: \b level, \b decoration
	
	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	
	\b Added by node emitter: \b opentag_head = "\n<h" + level, \b opentag_tail = ">", \b closetag = "</h" + level + ">", \b elementcontent set from child emitters
	
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->heading)
	@sa Muster_SimpleWiki_Parser::_heading_node
	@sa Muster_SimpleWiki_Emitter::_heading_emit
	*/
	const HEADING = 'heading';
	
	/** Emphasis node (\<em>).
	No parsed properties.
	
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->emph)
	@sa Muster_SimpleWiki_Parser::_emph_node
	@sa Muster_SimpleWiki_Emitter::_emphasis_emit
	*/
	const EMPHASIS = 'emphasis';
	
	/** Strong node (\<strong>).
	No parsed properties
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->strong)
	@sa Muster_SimpleWiki_Parser::_strong_node
	@sa Muster_SimpleWiki_Emitter::_strong_emit
	*/
	const STRONG = 'strong';
	
	/** Linebreak node (\<br />).
	No parsed properties
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->linebreak)
	@sa Muster_SimpleWiki_Parser::_linebreak_node
	@sa Muster_SimpleWiki_Emitter::_linebreak_emit
	*/
	const LINEBREAK = 'linebreak';
	
	/** Horizontal rule node (\<hr />).
	No parsed properties
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->horizontalrule)
	@sa Muster_SimpleWiki_Parser::_horizontalrule_node
	@sa Muster_SimpleWiki_Emitter::_horizontalrule_emit
	*/
	const HORIZONTALRULE = 'horizontalrule';
	
	/** Link node (\<a>).
	\b Parsed properties: \b target, \b title, \b decoration
	
	\b Added by node emitter: \b linkparts->anchor, \b linkparts->internaladdress, \b linkparts->symlink, \b linkparts->internalselector,  \b linkparts->internalversion, \b linkparts->symlinkpath (set by handlers), \b linkparts->externaladdress, \b linkparts->externalprotocol, \b linkparts->externalselector,  \b linkparts->rawlink, \b linkparts->rawlinkaddress (set by handlers)
	
	\b Added by node emitter: \b opentag_head = "\n<a", \b opentag_tail = ">", \b closetag = "</a>", \b caption set from child emitters
	
	\b Added by node emitter: \b unknown = FALSE; set to TRUE if link doesn't parse, and symlink/rawlink handlers absent. If set to true, causes link to appear underlined and appended with superscript "?"
	
	\b caption set by target if both caption and linkparts->anchor are empty

	\b Added by registered symlinks: \b linkparts->symlinkpath (or must be added by registered symlink handler)
	
	\b Added by registered rawlink handler: \b linkparts->rawlinkaddress
	
	\b elementcontent set by caption
	
	\b Added by prepare_link_node: \b linkparts->anchor ("#" stripped out if linkparts->anchor is set, and caption is empty)
	
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->link)
	@sa Muster_SimpleWiki_Parser::_link_node
	@sa Muster_SimpleWiki_Emitter::_link_emit
	*/
	const LINK = 'link';
	
	/** Image node (\<img />).
	\b Parsed properties: \b target, \b title, \b decoration
	
	\b Added by node emitter: \b linkparts->internaladdress, \b linkparts->symlink, \b linkparts->internalselector, \b linkparts->externaladdress, \b linkparts->externalprotocol, \b linkparts->externalselector, \b linkparts->rawlink, \b linkparts->rawlinkaddress
	
	\b Added by node emitter: \b opentag_head = "\n<img", \b opentag_tail = "/>", \b caption set from child emitters
	
	\b Added by registered symlinks: \b linkparts->symlinkpath (or must be added by registered symlink handler)
	
	\b Added by prepare_image_node: \b attributes from decoration, classes, properties as styles

	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->image)
	@sa Muster_SimpleWiki_Parser::_image_node
	@sa Muster_SimpleWiki_Emitter::_image_emit
	*/
	const IMAGE = 'image';
	
	/** Ordered list node (\<ol>).
	\b Parsed properties: \b level, \b decoration ($rules->list)

	\b Added by node emitter: \b opentag_head = "\n<ol", \b opentag_tail = "/>", \b closetag = "\n</ol>", \b elementcontent set from child emitters
	
	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules
	@sa Muster_SimpleWiki_Parser::_list_node
	@sa Muster_SimpleWiki_Emitter::_ordered_list_emit
	*/
	const ORDERED_LIST = 'ordered_list';
	
	/** Unordered list node (\<ul>).
	\b Parsed properties: \b level, \b decoration

	\b Added by node emitter: \b opentag_head = "\n<ul", \b opentag_tail = "/>", \b closetag = "\n</ul>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->list)
	@sa Muster_SimpleWiki_Parser::_list_node
	@sa Muster_SimpleWiki_Emitter::_unordered_list_emit
	*/
	const UNORDERED_LIST = 'unordered_list';
	
	/** List item node (\<li>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<li", \b opentag_tail = "/>", \b closetag = "\n</li>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->item)
	@sa Muster_SimpleWiki_Parser::_item_node
	@sa Muster_SimpleWiki_Emitter::_list_item_emit
	*/
	const LIST_ITEM = 'list_item';
	
	/** Definition list node (\<dl>).
	\b Parsed properties: \b level, \b decoration

	\b Added by node emitter: \b opentag_head = "\n<dl", \b opentag_tail = "/>", \b closetag = "\n</dl>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->deflist)
	@sa Muster_SimpleWiki_Parser::_deflist_node
	@sa Muster_SimpleWiki_Emitter::_def_list_emit
	*/
	const DEF_LIST = 'def_list';
	
	/** Definition term node (\<dt>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<dt", \b opentag_tail = "/>", \b closetag = "\n</dt>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->defitem)
	@sa Muster_SimpleWiki_Parser::_defitem_node
	@sa Muster_SimpleWiki_Emitter::_def_term_emit
	*/
	const DEF_TERM = 'def_term';
	
	/** Definition description node (\<dd>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<dd", \b opentag_tail = "/>", \b closetag = "\n</dd>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->defitem)
	@sa Muster_SimpleWiki_Parser::_defitem_node
	@sa Muster_SimpleWiki_Emitter::_def_desc_emit
	*/
	const DEF_DESC = 'def_desc';
	
	/** Table node (\<table>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<table", \b opentag_tail = "/>", \b closetag = "\n</table>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->table)
	@sa Muster_SimpleWiki_Parser::_table_node
	@sa Muster_SimpleWiki_Emitter::_table_emit
	*/
	const TABLE = 'table';
	
	/** Table row node (\<tr>).
	\b Parsed properties: \b decoration

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->table)
	@sa Muster_SimpleWiki_Parser::_table_node
	@sa Muster_SimpleWiki_Emitter::_table_row_emit
	*/
	const TABLE_ROW = 'table_row';
	
	/** Table headcell node (\<th>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<tr", \b opentag_tail = "/>", \b closetag = "\n</tr>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->cell)
	@sa Muster_SimpleWiki_Parser::_table_node
	@sa Muster_SimpleWiki_Emitter::_table_headcell_emit
	*/
	const TABLE_HEADCELL = 'table_headcell';
	
	/** Table data cell node (\<td>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<td", \b opentag_tail = "/>", \b closetag = "\n</td>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->cell)
	@sa Muster_SimpleWiki_Parser::_table_node
	@sa Muster_SimpleWiki_Emitter::_table_cell_emit
	*/
	const TABLE_CELL = 'table_cell';
	
	/** Span node (\<span>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<span", \b opentag_tail = "/>", \b closetag = "\n</span>", \b elementcontent set from child emitters

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->span)
	@sa Muster_SimpleWiki_Parser::_span_node
	@sa Muster_SimpleWiki_Emitter::_span_emit
	*/
	const SPAN = 'span';
	
	/** Blockdivider node (\<div>\</div>).
	\b Parsed properties: \b decoration

	\b Added by node emitter: \b opentag_head = "\n<div", \b opentag_tail = "/>", \b closetag = "\n</div>", \b elementcontent = ''

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules  ($rules->blockdivider)
	@sa Muster_SimpleWiki_Parser::_blockdivider_node
	@sa Muster_SimpleWiki_Emitter::_blockdivider_emit
	*/
	const BLOCKDIVIDER = 'blockdivider';
	
	/** Code node (\<code>).
	\b Parsed properties: \b textcontent, \b decoration

	\b Added by node emitter: \b opentag_head = "\n<code", \b opentag_tail = "/>", \b closetag = "\n</code>", \b elementcontent = textcontent (encoded if escapecontent)

	\b escapecontent = TRUE, may get altered by callout
	
	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->code)
	@sa Muster_SimpleWiki_Parser::_code_node
	@sa Muster_SimpleWiki_Emitter::_code_emit
	*/
	const CODE = 'code';
	
	/** Preformatted node (\<pre>).
	\b Parsed properties: \b textcontent, \b decoration

	\b Added by node emitter: \b opentag_head = "\n<pre", \b opentag_tail = "/>", \b closetag = "\n</pre>", \b elementcontent = textcontent (encoded if escapecontent)

	\b escapecontent = TRUE, may get altered by callout
	
	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->pre)
	@sa Muster_SimpleWiki_Parser::_pre_node
	@sa Muster_SimpleWiki_Emitter::_preformatted_emit
	*/
	const PREFORMATTED = 'preformatted';
	
	/** Blockdef node (various).
	\b Parsed properties: \b blocktag, \b decoration
	
	\b Added by node emitter: \b knowntag (can be over-ridden by blockdef handler)

	\b Added by node emitter: \b opentag_head = "\n<$blocktag", \b opentag_tail = "/>", \b closetag = "\n</$blocktag>", \b elementcontent set by child nodes, unless set by blockdef handler. dl... blocktags changed to xmlns form (dl:tag); unknown blocktag returns markup text.

	\b Added by prepare_node: \b attributes from decoration, classes, properties as styles
	@sa Muster_SimpleWiki_Emitter::$_blocktags for a list of blocktags.
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->blockdef)
	@sa Muster_SimpleWiki_Parser::_blockdef_node
	@sa Muster_SimpleWiki_Emitter::_blockdef_emit
	*/
	const BLOCKDEF = 'blockdef';
	
	/** Macro definition. 
	\b parsed properties: \b macroname, \b textcontent, \b decoration.
	
	\b added by node emitter: \b processed. Automatically set to TRUE 
	if a macro processor found, otherwise set to false for internal processing control (error).
	
	\b added by macro: \b output. If output is not set, caption is used for text output if available.
	@sa Muster_SimpleWiki_Parser::_set_rules ($rules->macro, $rules->blockmacro)
	@sa Muster_SimpleWiki_Parser::_macro_node
	@sa Muster_SimpleWiki_Parser::_blockmacro_node
	@sa Muster_SimpleWiki_Emitter::_macro_emit
	*/
	const MACRO = 'macro';
	
/**@}*/
/**@{ @name Standard node properties */
    /// A parent node in the document tree.
	public $parent;
	/// array of child nodes - used to emit content
	public $children;
	/// holds the type of node - See constants for types
	public $type;
/**@}*/
	/** Create new node.
	initialized children array, sets type and parent, and adds to parent children list.
	@param string $type selected from one of the class constants
	@param object $parent the parent of the node.
		
	*/
    public function __construct($type, $parent=NULL)
	{
        $this->children = array();
        $this->type = $type;
        $this->parent = $parent;
        if (!empty($parent))
            $parent->child_append($this);
	}
	/** Add child to children list.
		Children emit output as content for the current node.
		@param object $child
	*/
	protected function child_append($child)
	{
		$this->children[] = $child;
	}
	/** For debug: Returns an array of properties, leaving out parent and children properties.
	*/
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

