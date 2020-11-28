<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\vehicleinfo\acp;

/**
 * Vehicle Info ACP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\davidiq\vehicleinfo\acp\main_module',
			'title'		=> 'ACP_VEHICLEINFO_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_VEHICLEINFO',
					'auth'	=> 'ext_davidiq/vehicleinfo && acl_a_board',
					'cat'	=> ['ACP_VEHICLEINFO_TITLE'],
				],
			],
		];
	}
}
