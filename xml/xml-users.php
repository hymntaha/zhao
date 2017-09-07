<?php 

require_once(dirname(__FILE__) . '/../config.php');

function stringToInt($str) {
  $hash = sha1($str);
  $substring = substr($hash, 0,8); //ints are unsigned so we can only use the first 15 hex digits
  return hexdec($substring);
}

// THIS IS ABSOLUTELY ESSENTIAL - DO NOT FORGET TO SET THIS 
@date_default_timezone_set("GMT"); 

$writer = new XMLWriter(); 

$writer->openURI('php://output');
$writer->startDocument('1.0', 'utf-8');
$writer->setIndent(true);

$writer->startElement('sphinx:docset');

$writer->startElement('sphinx:schema');
{

  $writer->startElement('sphinx:field');
  $writer->writeAttribute('name', 'name');
  $writer->endElement();

  $writer->startElement('sphinx:field');
  $writer->writeAttribute('name', 'slug');
  $writer->endElement();

  $writer->startElement('sphinx:attr');
  $writer->writeAttribute('name', 'display_name');
  $writer->writeAttribute('type', 'string');
  $writer->endElement();


}
$writer->endElement(); //sphinx:schema

$cursor = user::find();
$docid = 0;
foreach ($cursor as $id => $user) {

  $docid++;
  $writer->startElement('sphinx:document');
  $writer->writeAttribute('id', $docid);
  $writer->writeElement('name', $user['username']);
  $writer->writeElement('display_name', $user['username']);
  $writer->writeElement('slug', $user['slug']);
  $writer->endElement();

}

$writer->endElement(); //sphinx:docset

$writer->endDocument(); 

$writer->flush(); 