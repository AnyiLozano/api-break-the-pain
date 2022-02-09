<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * This variable is used to reference the active status.
     * @var integer with the inactive status.
     */
    public $active_status = 1;

    /**
     * This variable is used to reference the inactive status.
     * @var integer with the inactive status.
     */
    public $inactive_status = 2;

    /**
     * This function is used from formatted the response that sent to the end user.
     * @param boolean $status with the status of the request.
     * @param array $message with the message that we sent to the end user.
     * @param array $data with the data obtained in the request.
     * @param array with the response formatted that we sent to the end user.
     */
    public function response(bool $status, array $message = [], $data = []): array
    {
        if ($message['type'] == "success") {
            $message['code'] = 200;
        } else if ($message['type'] == "error") {
            $message['code'] = 500;
        } else if ($message['type'] == "warning") {
            $message['code'] = 300;
        } else {
            abort(500);
        }

        return array('transaction' => array('status' => $status), 'message' => $message, 'data' => $data);
    }
}
