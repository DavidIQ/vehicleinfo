<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [

    'VEHICLEINFO_YEAR'      => 'Year',
	'VEHICLEINFO_MAKE'      => 'Make',
	'VEHICLEINFO_MODEL'     => 'Model',
	'VEHICLEINFO_TYPE'      => 'Type',
	'VEHICLEINFO_TYPE_ENTRY'    => 'Type (EX, LX, Extended Cab, etc.)',
	'VEHICLEINFO_PRICE'     => 'Price',
	'VEHICLEINFO_SALE_DATE' => 'Sale Date',
	'VEHICLEINFO_MARK_SOLD' => 'Mark Sold',
	'VEHICLEINFO_SELECT_MAKE'   => 'Select a make first',
	'VEHICLEINFO_ENTRY_REQUIRED'=> 'You must select a year and make or enter a subject',

	'VEHICLEINFO_NO_LISTING'        => 'No current listings.',
	'VEHICLEINFO_LIST_PAGE'			=> 'Vehicle List',

	'VEHICLEINFO_HELLO'		=> 'Hello %s!',
	'VEHICLEINFO_GOODBYE'		=> 'Goodbye %s!',

	'ACP_VEHICLEINFO_GOODBYE'			=> 'Should say goodbye?',
	'ACP_VEHICLEINFO_SETTING_SAVED'	=> 'Settings have been saved successfully!',
]);
