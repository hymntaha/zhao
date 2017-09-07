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
  $writer->writeAttribute('name', 'title');
  $writer->endElement();

  $writer->startElement('sphinx:field');
  $writer->writeAttribute('name', 'tag_text');
  $writer->endElement();

  $writer->startElement('sphinx:field');
  $writer->writeAttribute('name', 'username');
  $writer->endElement();

  $writer->startElement('sphinx:field');
  $writer->writeAttribute('name', 'description');
  $writer->endElement();

  $writer->startElement('sphinx:attr');
  $writer->writeAttribute('name', 'mongo_id');
  $writer->writeAttribute('type', 'string');
  $writer->endElement();

  $writer->startElement('sphinx:attr');
  $writer->writeAttribute('name', 'tag_attribute');
  $writer->writeAttribute('type', 'multi');
  $writer->endElement();

}
$writer->endElement(); //sphinx:schema

$cursor = microguide::find(array('status' => 'accepted'));
foreach ($cursor as $id => $microguide) {

  $mongoId = (string)$microguide['_id'];
  $sphinxid = stringToInt($mongoId);

  $writer->startElement('sphinx:document');
  $writer->writeAttribute('id', $sphinxid);
  $writer->writeElement('title', $microguide['title']); 
  $writer->writeElement('description', $microguide['description']); 
  $writer->writeElement('username', $microguide['author'] . ' ' . $microguide['authorSlug']); 
  $writer->writeElement('mongo_id', $mongoId); 
  $writer->writeElement('tag_text', implode(' ', $microguide['tags']));
  $writer->writeElement('tag_attribute', implode(',', array_map('stringToInt', $microguide['tags'])));
  $writer->endElement();
}

$writer->endElement(); //sphinx:docset

$writer->endDocument(); 

$writer->flush(); 
