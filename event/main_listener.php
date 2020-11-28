<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\vehicleinfo\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Vehicle Info Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return [
			'core.posting_modify_template_vars'		    => 'load_form',
            'core.posting_modify_submission_errors'     => 'check_errors',
            'core.submit_post_modify_sql_data'          => 'add_data',
            'core.viewtopic_assign_template_vars_before'    => 'set_topic_title',
            'core.viewtopic_modify_post_row'            => 'set_post_data',
            'core.viewforum_modify_topicrow'            => 'set_topic_title_viewforum'
		];
	}

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/** @var \davidiq\vehicleinfo\service */
	protected $service;

	/** @var string */
	protected $topics_table;

	/** @var \phpbb\routing\helper */
	protected $routing_helper;

    /**
     * Constructor
     *
     * @param \phpbb\language\language $language Language object
     * @param \phpbb\request\request $request Request object
     * @param \phpbb\template\template $template Template object
     * @param \phpbb\routing\helper $routing_helper Routing helper object
     * @param \davidiq\vehicleinfo\service $service Extension service
     * @param string $topics_table Topics table
     */
	public function __construct(\phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\routing\helper $routing_helper, \davidiq\vehicleinfo\service $service, string $topics_table)
	{
		$this->language = $language;
		$this->request  = $request;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
		$this->service  = $service;
		$this->topics_table = $topics_table;
	}

    /**
     * Load the vehicle info posting form
     *
     * @param \phpbb\event\data $event Event object
     */
	public function load_form(\phpbb\event\data $event)
    {
        $post_data = $event['post_data'];
        if (($post_data['post_id'] ?? 0) === ($post_data['topic_first_post_id'] ?? 0))
        {
            $this->language->add_lang('common', 'davidiq/vehicleinfo');
            $current_year = date('Y') + 1;
            $s_vehicleinfo_years = [];
            for ($y = ($current_year - 50); $y <= $current_year; $y++)
            {
                $s_vehicleinfo_years[] = $y;
            }

            $vehicleinfo_year = $this->request->variable('vi_year', (int) ($post_data['vehicle_year'] ?? 0));
            $vehicleinfo_make = $this->request->variable('vi_make', (int) ($post_data['vehicle_make_id'] ?? 0));
            $vehicleinfo_model = $this->request->variable('vi_model', (int) ($post_data['vehicle_model_id'] ?? 0));
            $vehicleinfo_type = $this->request->variable('vi_type', $post_data['vehicle_type'] ?? '');
            $vehicleinfo_price = $this->request->variable('vi_price', $post_data['vehicle_price'] ?? '');

            $this->template->assign_vars([
                'S_VEHICLEINFO'         => true,
                'VEHICLEINFO_YEAR'      => $vehicleinfo_year,
                'S_VEHICLEINFO_YEARS'   => $s_vehicleinfo_years,
                'VEHICLEINFO_MAKE'      => $vehicleinfo_make,
                'S_VEHICLEINFO_MAKES'   => $this->service->get_makes(),
                'VEHICLEINFO_MODEL'     => $vehicleinfo_model,
                'S_VEHICLEINFO_MODELS'  => $this->service->get_models($vehicleinfo_make),
                'VEHICLEINFO_TYPE'      => $vehicleinfo_type,
                'VEHICLEINFO_PRICE'     => $vehicleinfo_price,
                'VEHICLEINFO_AJAX_CALL' => str_replace('/0', '/', $this->routing_helper->route('davidiq_vehicleinfo_models', ['make_id' => 0]))
            ]);
        }
    }

    /**
     * Check submit errors
     *
     * @param \phpbb\event\data $event Event object
     */
    public function check_errors(\phpbb\event\data $event)
    {
        $post_data = $event['post_data'];
        $error = $event['error'];
        $empty_subject = $this->language->lang('EMPTY_SUBJECT');

        if (in_array($empty_subject, $error) && ($post_data['post_id'] ?? 0) === ($post_data['topic_first_post_id'] ?? 0))
        {
            unset($error[array_search($empty_subject, $error)]);

            $vehicleinfo_year = $this->request->variable('vi_year', $post_data['vehicle_year'] ?? 0);
            $vehicleinfo_make = $this->request->variable('vi_make', $post_data['vehicle_make_id'] ?? 0);

            if (empty($vehicleinfo_year) || empty($vehicleinfo_make))
            {
                $this->language->add_lang('common', 'davidiq/vehicleinfo');
                $error[] = $this->language->lang('VEHICLEINFO_ENTRY_REQUIRED');
            }
            $event['error'] = $error;
        }
    }

    /**
     * Add submit data
     *
     * @param \phpbb\event\data $event Event object
     */
    public function add_data(\phpbb\event\data $event)
    {
        $data = $event['data'];
        if (($data['post_id'] ?? 0) === ($data['topic_first_post_id'] ?? 0))
        {
            $sql_data = $event['sql_data'];
            $sql_data[$this->topics_table]['sql'] = array_merge($sql_data[$this->topics_table]['sql'],
            [
                'vehicle_year'      => $this->request->variable('vi_year', (int) $data['vehicle_year']),
                'vehicle_make_id'   => $this->request->variable('vi_make', (int) $data['vehicle_make_id']),
                'vehicle_model_id'  => $this->request->variable('vi_model', (int) $data['vehicle_model_id']),
                'vehicle_type'      => $this->request->variable('vi_type', (string) $data['vehicle_type']),
                'vehicle_price'     => $this->request->variable('vi_price', (string) $data['vehicle_price']),
            ]);
            $event['sql_data'] = $sql_data;
        }
    }

    /**
     * Sets the topic title where applicable
     *
     * @param \phpbb\event\data $event
     */
    public function set_topic_title(\phpbb\event\data $event)
    {
        $topic_data = $event['topic_data'];
        if (!empty($topic_data['vehicle_year']) && !empty($topic_data['vehicle_make_id']))
        {
            $topic_data['topic_title'] = $this->service->get_title($topic_data, $topic_data['topic_title']);
            $event['topic_data'] = $topic_data;
        }
    }

    /**
     * Sets the post title where applicable
     *
     * @param \phpbb\event\data $event
     */
    public function set_post_data(\phpbb\event\data $event)
    {
        $row = $event['row'];
        $topic_data = $event['topic_data'];
        if (!empty($topic_data['vehicle_year']) && !empty($topic_data['vehicle_make_id']) && $row['post_id'] == $topic_data['topic_first_post_id'])
        {
            $post_row = $event['post_row'];
            $post_row['POST_SUBJECT'] = $topic_data['topic_title'];
            $post_row = array_merge($post_row, [
                'S_VEHICLEINFO'  => true,
                'VEHICLEINFO_YEAR'  => $topic_data['vehicle_year'],
                'VEHICLEINFO_MAKE'  => $this->service->get_make_name((int) $topic_data['vehicle_make_id']),
                'VEHICLEINFO_MODEL' => $this->service->get_model_name((int) $topic_data['vehicle_make_id'], (int) $topic_data['vehicle_model_id']),
                'VEHICLEINFO_TYPE'  => $topic_data['vehicle_type'],
                'VEHICLEINFO_PRICE' => $topic_data['vehicle_price'],
                'VEHICLEINFO_SALE_DATE' => $this->service->get_sale_date($topic_data['vehicle_sale_time']),
                'VEHICLEINFO_MARK_SOLD_AJAX_CALL' => str_replace('/0', '/', $this->routing_helper->route('davidiq_vehicleinfo_mark_sold', ['topic_id' => 0])),
                'VEHICLEINFO_UNMARK_SOLD_AJAX_CALL' => str_replace('/0', '/', $this->routing_helper->route('davidiq_vehicleinfo_unmark_sold', ['topic_id' => 0]))
            ]);
            $event['post_row'] = $post_row;
            $this->language->add_lang('common', 'davidiq/vehicleinfo');
        }
    }

    /**
     * Sets the topic title in viewforum
     *
     * @param \phpbb\event\data $event
     */
    public function set_topic_title_viewforum(\phpbb\event\data $event)
    {
        $row = $event['row'];
        if (!empty($row['vehicle_year']) && !empty($row['vehicle_make_id']))
        {
            $topic_row = $event['topic_row'];
            $topic_row['TOPIC_TITLE'] = $topic_row['LAST_POST_SUBJECT'] = $row['topic_title'] = $row['topic_last_post_subject'] = $this->service->get_title($row, $topic_row['TOPIC_TITLE']);
            $event['topic_row'] = $topic_row;
        } else if (empty($row['topic_last_post_subject']))
        {
            print_r($row);
        }
    }
}
