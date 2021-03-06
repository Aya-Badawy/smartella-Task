<?php
/*
@param $frame_link | string , $page | number.
@return $new_link | string.
@func take iframe link edit it to add page number to it.
*/

function edit_iframe_link($frame_link,$page){
$link=htmlentities($frame_link);

$index_in_orginal=0;
preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $link, $matches);
$splited_link=$matches;
$len=count($splited_link[0]);
for($i=0;$i<$len;$i++){

if(strpos($splited_link[0][$i], ".")){

$index_in_orginal=$i;
}
}
$splited_link[0][$index_in_orginal]=str_replace( array('t;','"',"&quo"), '', $splited_link[0][$index_in_orginal]);
$splited_link[0][$index_in_orginal]= $splited_link[0][$index_in_orginal] ."#page=".$page;
$new_link='';
for($i=0;$i<$len;$i++){
 $new_link .= $splited_link[0][$i]." ";
}
 return html_entity_decode($new_link);
}
