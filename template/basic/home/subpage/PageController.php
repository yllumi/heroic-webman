<?php namespace app\pages\home\subpage;

use Yllumi\HeroicWebman\Attributes\FrontendRoute;
use Yllumi\HeroicWebman\BaseController;

#[FrontendRoute()]
class PageController extends BaseController
{
    public $data = [
        'page_title' => 'Subpage'
    ];

    public function getData()
    {
        $this->data['message'] = 'Selamat datang di Subpage!';

        return json($this->data);
    }
}
