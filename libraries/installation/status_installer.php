<?php

namespace IllinoisPublicMedia\NprStoryApi\Libraries\Installation;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

use ExpressionEngine\Model\Status\Status;

/**
 * Installs statuses required by NPR Story API module.
 */
class status_installer
{
    private $required_statuses = array(
        'draft'
    );

    /**
     * Create status.
     *
     * @return void
     */
    public function install($status_names)
    {
        foreach ($status_names as $name) {
            if (!in_array($name, $this->required_statuses)) {
                throw new \Exception("Status configuration not found for {$name}.");
            }

            $this->update_status_data($name);
        }
    }

    /**
     * Uninstall statuses created by NPR Story API.
     *
     * @return void
     */
    public function uninstall()
    {
        foreach (array_values($this->required_statuses) as $name) {
            $model = ee('Model')->get('Status')->filter('status', '==', $name)->first();
            $model->delete();
        }
    }

    private function create_draft($status = null) {
        $data = array(
            'status' => 'draft',
            'highlight' => 'ffcc00'
        );

        if ($status == null) {
            $status = ee('Model')->make('Status');
        }

        foreach ($data as $key => $val) {
            $status->{$key} = $val;
        }

        $status->save();
    }
    
    private function update_status_data($status_name)
    {
        $status = ee('Model')
            ->get('Status')
            ->filter('status', '==', $status_name)
            ->first();

        switch($status_name) {
            case 'draft':
                $this->create_draft($status);
                break;
            default:
                throw new \Exception("No status initializer found for {$status_name}.");
        }
    }
}