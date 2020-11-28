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

class install_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'vehicle_make') && $this->db_tools->sql_table_exists($this->table_prefix . 'vehicle_model');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	/**
	 * Update database schema.
	 *
	 * @return array Array of schema changes
	 */
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'vehicle_make'	=> [
					'COLUMNS'		=> [
						'vehicle_make_id'			=> ['UINT', null, 'auto_increment'],
						'vehicle_make_name'			=> ['VCHAR:255', ''],
					],
					'PRIMARY_KEY'	=> 'vehicle_make_id',
				],
				$this->table_prefix . 'vehicle_model'	=> [
					'COLUMNS'		=> [
						'vehicle_model_id'			=> ['UINT', null, 'auto_increment'],
						'vehicle_model_name'		=> ['VCHAR:255', ''],
                        'vehicle_make_id'           => ['UINT', 0]
					],
					'PRIMARY_KEY'	=> 'vehicle_model_id',
				],
			],
			'add_columns'	=> [
				$this->table_prefix . 'topics'		=> [
                    'vehicle_year'                  => ['INT:4', 0],
					'vehicle_make_id'				=> ['UINT', 0],
                    'vehicle_model_id'				=> ['UINT', 0],
                    'vehicle_type'                  => ['VCHAR:50', ''],
                    'vehicle_price'                 => ['VCHAR:25', ''],
                    'vehicle_sale_time'             => ['INT:11', 0],
				],
			],
		];
	}

	/**
	 * Revert database schema changes.
	 *
	 * @return array Array of schema changes
	 */
	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'topics'			=> [
                    'vehicle_year',
					'vehicle_make_id',
                    'vehicle_model_id',
                    'vehicle_type',
                    'vehicle_price',
				],
			],
			'drop_tables'		=> [
				$this->table_prefix . 'vehicle_make',
                $this->table_prefix . 'vehicle_model'
			],
		];
	}
}
