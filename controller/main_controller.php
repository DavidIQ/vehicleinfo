<?php
/**
 *
 * Vehicle Info. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\vehicleinfo\controller;

/**
 * Vehicle Info main controller.
 */
class main_controller
{
    /** @var \davidiq\vehicleinfo\service */
    protected $service;

    /**
     * Constructor
     *
     * @param \davidiq\vehicleinfo\service $service  The extension's service object
     */
	public function __construct(\davidiq\vehicleinfo\service $service)
	{
		$this->service	= $service;
	}

    /**
     * Controller handler for route /make/{make_id}
     *
     * @param int $make_id
     */
	public function handleModels(int $make_id)
    {
        $json = new \phpbb\json_response();
        $models = array_map(function ($model)
        {
            return [
                'id' => $model['vehicle_model_id'],
                'name' => $model['vehicle_model_name']
            ];
        }, $this->service->get_models($make_id) ?? []);
        $json->send(array_values($models));
    }

    /**
     * Controller handler for route /sold/{topic_id}
     *
     * @param int $topic_id
     */
    public function handleMarkSold(int $topic_id)
    {
        $json = new \phpbb\json_response();
        $sold_datetime = $this->service->mark_sold($topic_id);
        $json->send($sold_datetime);
    }

    /**
     * Controller handler for route /unsold/{topic_id}
     *
     * @param int $topic_id
     */
    public function handleUnmarkSold(int $topic_id)
    {
        $this->service->unmark_sold($topic_id);
        $json = new \phpbb\json_response();
        $json->send([true]);
    }
}
