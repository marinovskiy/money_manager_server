<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 10:42 AM
 */

namespace AppBundle\Manager;


use Symfony\Component\HttpFoundation\Request;

class DeviceManager
{
    public function getDeviceByRequest(Request $request)
    {
        return $this->getDeviceByUdid($this->getUdidByRequest($request));
    }


}