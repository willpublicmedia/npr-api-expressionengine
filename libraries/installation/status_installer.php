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
    private $required_statuses = array(
        'draft' => null
    );

    public function __construct()
    {
        $this->required_statuses = $this->load_required_statuses();
    }

    /**
     * Create status.
     *
     * @return void
     */
    public function install($status_names)
    {
        foreach ($status_names as $name) {
            if (!array_key_exists($name, $this->required_statuses)) {
                throw new Exception("Status configuration not found for {$name}.");
            }

            $data = $this->required_statuses[$name];
            $data->save();
        }
    }

    /**
     * Uninstall statuses created by NPR Story API.
     *
     * @return void
     */
    public function uninstall()
    {
        foreach (array_values($this->required_statuses) as $model) {
            $model->delete();
        }
    }

    private function create_draft($status = null) {
        $data = array(
            'status' => 'draft',
            'highlight' => 'ffcc00',
            'status_order' => 2
        );

        if ($status == null) {
            $status = ee('Model')->make('Status');
        }

        foreach ($data as $key => $val) {
            $status->{$key} = $val;
        }

        return $status;
    }
    
    private function load_required_statuses() {
        $statuses = $this->required_statuses;

        foreach ($statuses as $name => $status) {
            $status = $this->load_status_data($name);
            $statuses[$name] = $status;
        }

        return $statuses;
    }

    private function load_status_data($status_name)
    {
        $status = ee('Model')
            ->get('Status')
            ->filter('status', '==', $status_name)
            ->first();

        switch($status_name) {
            case 'draft':
                $status = $this->create_draft($status);
                break;
            default:
                throw new Exception("No status initializer found for {$status_name}.");
        }

        return $status;
    }
}