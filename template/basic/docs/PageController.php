<?php namespace app\pages\docs;

use Yllumi\HeroicWebman\Attributes\FrontendRoute;
use Yllumi\HeroicWebman\BaseController;

#[FrontendRoute()]
class PageController extends BaseController
{
    public $data = [
        'page_title' => 'Documentation'
    ];

}
