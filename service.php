<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\vehicleinfo;

/**
 * Vehicle Info Service info.
 */
class service
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string */
	protected $table_prefix;

    /**
     * Constructor
     *
     * @param \phpbb\cache\service $cache
     * @param \phpbb\db\driver\driver_interface $db
     * @param \phpbb\user $user
     * @param \phpbb\template\template $template
     * @param string $table_prefix The db table prefix
     */
	public function __construct(\phpbb\cache\service $cache, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\template\template $template, string $table_prefix)
	{
	    $this->cache = $cache;
	    $this->db = $db;
	    $this->user = $user;
	    $this->template = $template;
		$this->table_prefix = $table_prefix;
	}

	/**
	 * Get list of vehicle makes
	 *
	 * @return array $vehicleinfo_makes list of vehicle makes
	 */
	public function get_makes()
	{
        if (($vehicleinfo_makes = $this->cache->get_driver()->get('vehicleinfo_makes')) === false)
        {
            $sql = "SELECT * FROM {$this->table_prefix}vehicle_make ORDER BY vehicle_make_name";
            $result = $this->db->sql_query($sql);
            $vehicleinfo_makes = $this->db->sql_fetchrowset($result);
            $this->db->sql_freeresult($result);
            $this->cache->get_driver()->put('vehicleinfo_makes', $vehicleinfo_makes);
        }
        return $vehicleinfo_makes;
	}

    /**
     * Gets a make's name
     *
     * @param int $make_id
     *
     * @return string the make name
     */
	public function get_make_name(int $make_id)
    {
        $make = array_filter($this->get_makes(), function($make) use ($make_id)
        {
           return (int)$make['vehicle_make_id'] === $make_id;
        });

        return count($make) ? reset($make)['vehicle_make_name'] : '';
    }

    /**
     * Get list of vehicle makes
     *
     * @param int $make_id The vehicle make for which to get models
     * @return array list of vehicle makes
     */
    public function get_models(int $make_id)
    {
        if (!$make_id)
        {
            return [];
        }

        if (($vehicleinfo_models = $this->cache->get_driver()->get('vehicleinfo_models')) === false)
        {
            $sql = "SELECT * FROM {$this->table_prefix}vehicle_model ORDER BY vehicle_model_name";
            $result = $this->db->sql_query($sql);
            $vehicleinfo_models = $this->db->sql_fetchrowset($result);
            $this->db->sql_freeresult($result);
            $this->cache->get_driver()->put('vehicleinfo_models', $vehicleinfo_models);
        }
        return array_filter($vehicleinfo_models, function($model) use ($make_id)
        {
            return (int) $model['vehicle_make_id'] === $make_id;
        });
    }

    /**
     * Get the vehicle model name
     *
     * @param int $make_id
     * @param int $model_id
     *
     * @return string vehicle model name
     */
    public function get_model_name(int $make_id, int $model_id)
    {
        $models = $this->get_models($make_id);
        $model = array_filter($models, function($model) use ($model_id)
        {
            return (int)$model['vehicle_model_id'] === $model_id;
        });

        return count($model) ? reset($model)['vehicle_model_name'] : '';
    }

    /**
     * Get the listing title
     *
     * @param array $topic_data the topic data from which to get the vehicle info
     * @param string $title the original title
     * @return string the topic title based on the vehicle info
     */
    public function get_title(array $topic_data, string $title)
    {
        $title_parts = array_filter([
            $topic_data['vehicle_year'],
            $this->get_make_name((int) $topic_data['vehicle_make_id']),
            $this->get_model_name((int) $topic_data['vehicle_make_id'], (int) $topic_data['vehicle_model_id']),
            $topic_data['vehicle_type']
        ], function($part) { return !empty($part); });

        return join(' ', $title_parts) . (!empty($title) ? " - $title" : '');
    }

    /**
     * Gets the sale date/time
     *
     * @param int $sale_time
     * @return mixed
     */
    public function get_sale_date(int $sale_time)
    {
        return !empty($sale_time) ? $this->user->format_date($sale_time) : false;
    }

    /**
     * Marks listing sold
     *
     * @param int $topic_id the listing
     * @return mixed formatted date time
     */
    public function mark_sold(int $topic_id)
    {
        $icons = $this->cache->obtain_icons();
        $tick_icon = array_filter($icons, function($icon)
        {
            return isset($icon['img']) && strstr($icon['img'], 'tick.gif') !== false;
        }) ?? [0];
        $tick_icon_id = array_keys($tick_icon)[0];
        $sold_time = time();
        $sql = "UPDATE {$this->table_prefix}topics t
                JOIN {$this->table_prefix}posts p ON p.topic_id = t.topic_id AND p.post_id = t.topic_first_post_id
                SET t.vehicle_sale_time = $sold_time, t.icon_id = {$tick_icon_id}, p.icon_id = {$tick_icon_id}
                WHERE t.topic_id = $topic_id";
        $result = $this->db->sql_query($sql);
        $this->db->sql_freeresult($result);

        return $this->get_sale_date($sold_time);
    }

    /**
     * Unmark listing as sold
     *
     * @param int $topic_id the listing ID
     */
    public function unmark_sold(int $topic_id)
    {
        $sql = "UPDATE {$this->table_prefix}topics t
                JOIN {$this->table_prefix}posts p ON p.topic_id = t.topic_id AND p.post_id = t.topic_first_post_id
                SET t.vehicle_sale_time = 0, t.icon_id = 0, p.icon_id = 0
                WHERE t.topic_id = $topic_id";
        $result = $this->db->sql_query($sql);
        $this->db->sql_freeresult($result);
    }

    /**
     * Load the car listing
     */
    public function load_list()
    {
        $sql_array = [
            'SELECT'	=> 't.*, p.*',
            'FROM'		=> [
                "{$this->table_prefix}topics"	=> 't',
            ],
            'LEFT_JOIN'	=> [
                [
                    'FROM'	=> ["{$this->table_prefix}posts" => 'p'],
                    'ON'	=> 'p.topic_id = t.topic_id'
                ]
            ],
            'WHERE'		=> 't.topic_first_post_id = p.post_id AND t.vehicle_make_id > 0 AND t.vehicle_model_id > 0',
            'ORDER_BY'  => 't.topic_time'
        ];

        $sql = $this->db->sql_build_query('SELECT', $sql_array);
        $result = $this->db->sql_query($sql);
        $update_count = $vehiclelist = $attach_list = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            if ($row['post_attachment'])
            {
                $attach_list[] = (int) $row['post_id'];
            }
            $vehiclelist[] = $row;
        }
        $this->db->sql_freeresult($result);

        if (count($attach_list))
        {
            $sql = "SELECT *
                FROM {$this->table_prefix}attachments
                WHERE {$this->db->sql_in_set('post_msg_id', $attach_list)}
                    AND in_message = 0
                ORDER BY attach_id DESC, post_msg_id ASC";
            $result = $this->db->sql_query($sql);

            while ($row = $this->db->sql_fetchrow($result))
            {
                $attachments[$row['post_msg_id']][] = $row;
            }
            $this->db->sql_freeresult($result);
        }

        $icons = $this->cache->obtain_icons();

        foreach($vehiclelist as $row)
        {
            $parse_flags = OPTION_FLAG_BBCODE | OPTION_FLAG_SMILIES;
            $description = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $parse_flags, true);

            if (!empty($attachments[$row['post_id']]))
            {
                parse_attachments($row['forum_id'], $description, $attachments[$row['post_id']], $update_count);
            }

            $this->template->alter_block_array('vehiclelist', [
                'TITLE'                 => $this->get_title($row, $row['topic_title']),
                'DESCRIPTION'           => $description,
                'POST_ICON_IMG'			=> ($row['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['img'] : '',
                'POST_ICON_IMG_WIDTH'	=> ($row['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['width'] : '',
                'POST_ICON_IMG_HEIGHT'	=> ($row['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['height'] : '',
                'POST_ICON_IMG_ALT' 	=> ($row['enable_icons'] && !empty($row['icon_id'])) ? $icons[$row['icon_id']]['alt'] : '',
                'S_HAS_ATTACHMENTS'	    => !empty($attachments[$row['post_id']]),
                'S_MULTIPLE_ATTACHMENTS'=> !empty($attachments[$row['post_id']]) && count($attachments[$row['post_id']]) > 1,
                'VEHICLEINFO_YEAR'      => $row['vehicle_year'],
                'VEHICLEINFO_MAKE'      => $this->get_make_name((int) $row['vehicle_make_id']),
                'VEHICLEINFO_MODEL'     => $this->get_model_name((int) $row['vehicle_make_id'], (int) $row['vehicle_model_id']),
                'VEHICLEINFO_TYPE'      => $row['vehicle_type'],
                'VEHICLEINFO_PRICE'     => $row['vehicle_price'],
            ]);

            if (!empty($attachments[$row['post_id']]))
            {
                foreach ($attachments[$row['post_id']] as $attachment)
                {
                    $this->template->assign_block_vars('vehiclelist.attachment', array(
                            'DISPLAY_ATTACHMENT'	=> $attachment)
                    );
                }
            }
        }

        if (count($update_count))
        {
            $sql = "UPDATE {$this->table_prefix}attachments
			SET download_count = download_count + 1
			WHERE {$this->db->sql_in_set('attach_id', array_unique($update_count))}";
            $this->db->sql_query($sql);
        }
    }
}
