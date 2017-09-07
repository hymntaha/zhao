<?php 


$_D['forms'] = array(

'share' => array(

  'title' => array(

  'tip' => 'Your Story Title',
  'help' => 'Capitalize The First Letter of All Major Words Please',

  'error_none' => 'You must specify a title',
  'error_long' => 'Your title cannot exceed 40 characters',
  'error_invalid' => 'Invalid title',
  'error_duplicate' => 'That title already exists'
  ),
  'text' => array(

    'tip' => 'Body of Your Story: Minimum 1 Word, Maximum 500 = Words',
    'help' => 'Keep a blank line between separate paragraphs.',

    'error_none' => 'You must specify a body',
    'error_invalid' => 'Invalid story body',
    'error_less' => 'Not enough words',
    'error_more' => 'Too many words'

  ),

  'tags' => array(
    'tip' => 'food, sushi, F train, Japanese, 2nd Ave stop, lunch specials, date spot, restaurant, soba noodles',
    'help' => 'Separate by commas or return key',
    'error_less' => 'You must specify at least 1 tag',
    'error_more' => 'You cannot have more than 20 tags',
    'error_invalid' => 'Invalid tag "%content"'
  ),

  'location' => array(
    'tip' => 'Enter a location',
    'help' => 'Place name, address, city or county',
    'error_none' => 'Please specify a location'
  ),

/*
  'address' => array(
    'tip' => '14 Market st, unit 100',
    'help' => 'Street name and number of this story (capitalize proper nouns)',
  ),
  'city' => array(
    'tip' => 'San Francisco',
    'help' => 'City of this story (capitalize proper nouns)',
  ),
  'hood' => array(
    'tip' => 'SOMA',
    'help' => 'Neighborhood area of this story (capitalize proper nouns)',
  ),
  'state' => array(
    'tip' => 'California',
    'help' => 'State or Province of this story (capitalize proper nouns)',
  ),
  'country' => array(
    'tip' => 'USA',
    'help' => 'Country of this story (capitalize proper nouns)',
  ),
*/

  'phone' => array(
    'tip' => '415-555-1212 (this will appear on the public site)',
    'help' => 'Include country code and use dashes between numbers',
  ),
  'url' => array(
    'tip' => 'www.yoursite.com',
    'help' => 'www. or http:// is not needed',
  ),
  'caption' => array(
    'tip' => 'photo caption',
    'help' => 'Caption for this photo'
  ),
  'comment' => array(
    'tip' => 'comment here',
    'help' => 'comments are for you and the editor only'
  )

  ),

  'microguide' => array(
    'title' => array(
      'tip' => 'Best eats in Dumbo, Outdoor play areas SF',
      'help' => '',
      'error_none' => '',
      'error_long' => '',
      'error_invalid' => '',
      'error_duplicate' => '',
    ),
    'description' => array(
      'tip' => 'Description must be no more than 40 words',
      'help' => '',
      'error_none' => '',
      'error_long' => '',
      'error_invalid' => '',
      'error_duplicate' => '',
    ),
  )
);
