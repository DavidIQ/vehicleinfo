<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\vehicleinfo\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['davidiq_vehicleinfo_goodbye']);
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_VEHICLEINFO_TITLE'
			]],
			['module.add', [
				'acp',
				'ACP_VEHICLEINFO_TITLE',
				[
					'module_basename'	=> '\davidiq\vehicleinfo\acp\main_module',
					'modes'				=> ['settings'],
				],
			]],
		];
	}
}
