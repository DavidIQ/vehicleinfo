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

class install_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		$sql = 'SELECT COUNT(*) AS cnt
			FROM ' . $this->table_prefix . "vehicle_make";
		$result = $this->db->sql_query($sql);
		$make_count = $this->db->sql_fetchfield('cnt');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(*) AS cnt
			FROM ' . $this->table_prefix . "vehicle_model";
		$result = $this->db->sql_query($sql);
		$model_count = $this->db->sql_fetchfield('cnt');
		$this->db->sql_freeresult($result);

		return $make_count > 0 && $model_count > 0;
	}

	public static function depends_on()
	{
		return ['\davidiq\vehicleinfo\migrations\install_schema'];
	}

	/**
	 * Add vehicle data
	 *
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		return [
			['custom', [[$this, 'add_vehicle_data']]],
		];
	}

	/**
	 * Adds vehicle data
	 */
	public function add_vehicle_data()
	{
		$vehicle_data = [
		    'Acura' => [
		        'Integra',
                'RL',
                'RSX',
                'TL',
                'TSX',
            ],
            'Audi'  => [
                'A3',
                'A4',
                'A5',
                'A6',
                'A7',
                'A8',
            ],
            'BMW'   => [
                '1 Series',
                '2 Series',
                '3 Series',
                '4 Series',
                '5 Series',
                '6 Series',
                '7 Series',
                '8 Series',
                'M',
                'M2',
                'M3',
                'M4',
                'M5',
                'M6',
                'X1',
                'X2',
                'X3',
                'X4',
                'X5',
                'X6',
                'X7',
            ],
            'Buick' => [
                'Enclave',
                'Encore',
                'LaCrosse',
                'LeSabre',
                'Regal',
                'Verano',
            ],
            'Cadillac'  => [
                'ATS',
                'Catera',
                'CT4',
                'CT5',
                'CT6',
                'Escalade',
                'SRX',
                'STS',
                'XLR',
            ],
            'Chevrolet' => [
                '1500',
                '2500',
                '3500',
                'Astro',
                'Aveo',
                'Blazer',
                'Camaro',
                'Caprice',
                'Cavalier',
                'Cobalt',
                'Colorado',
                'Equinox',
                'Impala',
                'Lumina',
                'Malibu',
                'Metro',
                'S10',
                'Silverado',
                'Suburban',
                'Tahoe',
                'Trailblazer',
                'Venture',
                'Volt',
            ],
            'Chrysler'  => [
                '200',
                '300',
                '300M',
                'Grand Voyager',
                'Pacifica',
                'Prowler',
                'PT Cruiser',
                'Sebring',
                'Town & Country',
                'Voyager',
            ],
            'Dodge'     => [
                'Avenger',
                'Caravan',
                'Charger',
                'Challenger',
                'Dakota',
                'Daytona',
                'Durango',
                'Grand Caravan',
                'Nitro',
                'Ram',
                'Rally',
            ],
            'Ford'      => [
                'Bronco',
                'E150',
                'E250',
                'E350',
                'Econoline',
                'Escape',
                'Escort',
                'Excursion',
                'Expedition',
                'Explorer',
                'F150',
                'F250',
                'F350',
                'F450',
                'Fiesta',
                'Focus',
                'Fusion',
                'Mustang',
                'Ranger',
                'Taurus',
            ],
            'GMC'       => [
                '1500',
                '2500',
                '3500',
                'Acadia',
                'Canyon',
                'Envoy',
                'Jimmy',
                'Savana',
                'Sierra',
                'Sonoma',
                'Suburban',
                'Yukon',
            ],
		    'Honda' => [
		        'Accord',
                'Civic',
                'CR-V',
                'Fit',
                'Insight',
                'Odyssey',
                'Ridgeline',
            ],
            'Hyundai'   => [
                'Accent',
                'Elantra',
                'Genesis',
                'Santa Fe',
                'Sonata',
                'Tiburon',
            ],
            'INFINITY'  => [
                'EX',
                'Q40',
                'Q50',
                'Q60',
                'Q70',
                'QX',
                'QX70',
                'QX80',
            ],
            'Isuzu'     => [
                'Amigo',
                'Axiom',
                'Rodeo',
            ],
            'Jeep'      => [
                'Cherokee',
                'Commander',
                'Compass',
                'Liberty',
                'Renegade',
                'Wrangler'
            ],
            'Kia'       => [
                'Forte',
                'Optima',
                'Rio',
                'Sedona',
                'Sephia',
                'Sorento',
                'Soul',
                'Spectra',
                'Sportage',
            ],
            'Land Rover'    => [
                'Defender',
                'Discovery',
                'Range Rover',
            ],
            'Lexus'     => [
                'ES',
                'GS',
                'IS',
                'LX',
                'RX',
            ],
            'Lincoln'   => [
                'Aviator',
                'Continental',
                'MKS',
                'MKZ',
                'Navigator',
                'Zephyr',
            ],
            'MAZDA'     => [
                'CX-3',
                'CX-5',
                'CX-7',
                'CX-9',
                'MAZDA2',
                'MAZDA3',
                'MAZDA4',
                'MAZDA5',
                'MAZDA6',
                'MPV',
                'MX-3',
                'Protege',
                'Tribute',
            ],
            'Mercedes-Benz' => [
                'A-Class',
                'B-Class',
                'C-Class',
            ],
            'Mercury'   => [
                'Marauder',
                'Sable',
                'Topaz',
                'Villager',
            ],
            'Mitsubishi'    => [
                '3000GT',
                'Diamante',
                'Eclipse',
                'Galant',
                'Lancer',
                'Mirage',
                'Outlander',
            ],
            'Nissan'        => [
                'Altima',
                'Armada',
                'cube',
                'JUKE',
                'Maxima',
                'Pathfinder',
                'Rogue',
                'Sentra',
                'Titan',
                'Versa',
                'Xterra',
            ],
            'Oldsmobile'    => [
                'Achieva',
                'Alero',
                'Aurora',
            ],
            'Saturn'        => [
                'Ion',
                'SKY',
                'VUE',
            ],
            'Subaru'        => [
                'Forester',
                'Legacy',
                'Outback',
            ],
            'Suzuki'        => [
                'Aerio',
                'Esteem',
                'Grand Vitara',
                'Kizashi',
                'Reno',
                'Swift',
                'SX4',
                'Verona',
                'Vitara',
                'X-90',
                'XL-7',
            ],
            'Toyota'        => [
                '4Runner',
                'Avalon',
                'Camry',
                'Celica',
                'Corolla',
                'Echo',
                'Highlander',
                'Land Cruiser',
                'Matrix',
                'MR2',
                'Prius',
                'RAV4',
                'Sequoia',
                'Sienna',
                'Solara',
                'Supra',
                'Tacoma',
                'Tundra',
                'Yaris',
            ],
            'Volkswagen'    => [
                'Beetle',
                'Golf',
                'GTI',
                'Jetta',
            ],
            'Volvo'         => [
                'S40',
                'S60',
                'S70',
                'S80',
            ],
        ];
		foreach ($vehicle_data as $make => $models)
        {
            $sql = 'INSERT INTO ' . $this->table_prefix . 'vehicle_make ' . $this->db->sql_build_array('INSERT', [
                'vehicle_make_name' => $make
            ]);
            $this->db->sql_query($sql);
            $vehicle_make_id = $this->db->sql_nextid();
            foreach ($models as $model)
            {
                $sql = 'INSERT INTO ' . $this->table_prefix . 'vehicle_model ' . $this->db->sql_build_array('INSERT', [
                    'vehicle_model_name'		=> $model,
                    'vehicle_make_id'           => $vehicle_make_id,
                ]);
                $this->db->sql_query($sql);
            }
        }
	}
}
