<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use EllisLab\ExpressionEngine\Model\Status\Status;

/**
 * Installs statuses required by NPR Story API module.
 */
class status_installer
{
    private $status_data;

    public function __construct()
    {
        $this->status_data = $this->load_status_data();
    }

    /**
     * Create status.
     *
     * @return void
     */
    public function install($status_names)
    {
        foreach ($status_names as $name) {
            if (!array_key_exists($name, $this->status_data)) {
                throw new Exception("Status configuration not found for {$name}.");
            }

            $data = $this->status_data[$name];
            $this->init_status($data);
        }
    }

    /**
     * Uninstall statuses created by NPR Story API.
     *
     * @return void
     */
    public function uninstall()
    {
        foreach (array_values($this->status_data) as $model) {
            $model->delete();
        }
    }

    private function create_status($status_name)
    {
        $data = array(
            'status' => $status_name,
            'highlight' => 'ffcc00',
            'status_order' => 2,
        );

        $status = ee('Model')->make('Status', $data);

        return $status;
    }

    /**
     * Create a new status using a status model.
     *
     * @param  Status $model Status model.
     *
     * @return void
     */
    private function init_status($model)
    {
        $already_installed = ee('Model')->get('Status')
            ->filter('status', $model->status)
            ->count() > 0;

        if ($already_installed === false) {
            $model->save();
        }
    }

    private function load_status_data()
    {
        $draft = $this->create_status('draft');

        $statuses = array(
            'draft' => $draft,
        );

        return $statuses;
    }
}
