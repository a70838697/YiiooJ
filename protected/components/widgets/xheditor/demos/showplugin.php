<?php
//此程序为demo09的服务端显示演示程序
header('Content-Type: text/html; charset=utf-8');
$sHtml=$_POST['elm1'];
function fixPre($match)
{
	$match[2]=preg_replace('/<br\s*\/?>/i',"\r\n",$match[2]);
	$match[2]=preg_replace('/<\/?[\w:]+(\s+[^>]+?)?>/i',"",$match[2]);//去除所有HTML标签
	return $match[1].$match[2].$match[3];
}
$sHtml=preg_replace_callback('/(<pre(?:\s+[^>]*?)?>)([\s\S]+?)(<\/pre>)/i','fixPre',$sHtml);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>demo09插件显示测试页</title>
<style type="text/css">
body{margin:5px;border:2px solid #ccc;padding:5px;}
</style>
<link type="text/css" rel="stylesheet" href="syntaxhighlighter/SyntaxHighlighter.css"/>
<script type="text/javascript" src="syntaxhighlighter/shCore.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushXml.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushJScript.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushCss.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushPhp.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushCSharp.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushCpp.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushJava.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushPython.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushRuby.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushVb.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushDelphi.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushSql.js"></script>
<script type="text/javascript" src="syntaxhighlighter/shBrushPlain.js"></script>
<script type="text/javascript">
window.onload=function(){dp.SyntaxHighlighter.HighlightAll('code');}
</script>
<body>
	<?php echo $sHtml?>
</body>
</html>