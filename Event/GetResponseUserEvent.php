<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\AjaxFOSUserBundle\Event;

use Symfony\Component\HttpFoundation\Response;
/**
 * Description of GetResponseUserEvent
 *
 * @author matias
 */
class GetResponseUserEvent extends UserEvent {
    //put your code here
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
