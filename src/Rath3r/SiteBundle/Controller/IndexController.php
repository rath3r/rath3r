<?php

namespace Rath3r\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{

    public function __construct()
    {

    }

    public function indexAction()
    {
        $wordpressUrl = 'https://public-api.wordpress.com/rest/v1.1/sites/blog.rath3r.com/posts/';
        $json = file_get_contents($wordpressUrl);
        $obj = json_decode($json);;
        $posts = $obj->posts;
        //var_dump($posts);
        return $this->render(
            '/default/index.html.twig',
            $posts
        );
    }
}
