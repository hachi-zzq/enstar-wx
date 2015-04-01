<?php

use Enstar\Library\Read\ReadReportRender;
class HomeController extends BaseController {


    public function test()
    {
        return 1;
        $read = new ReadReportRender(27,"C:/Users/zhu/Desktop/ssh-download/388_4gOlzxs0.json");
        print_r($read->getCacheRenderJson());

    }
}
