<?php

namespace spec\Rath3r\SiteBundle\Controller;

use Rath3r\SiteBundle\Controller\IndexController;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IndexController::class);
    }

    function its_indexAction_should_render_the_index_page() {

        //$this->render()->shouldBeCalled();
        //$this->shouldReturn(Instance);
        $this->indexAction()->shouldReturn(Response::class);
    }

    function its_indexAction_should_render_the_index_page_with_posts() {

    }
}
