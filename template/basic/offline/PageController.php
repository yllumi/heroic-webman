<?php namespace app\pages\offline;

use Yllumi\HeroicWebman\BaseController;
use Yllumi\HeroicWebman\Attributes\FrontendRoute;

#[FrontendRoute(route: 'offline', preload: true)]
class PageController extends BaseController
{
    public $data = [
        'page_title' => 'You are Offline'
    ];
   
}
