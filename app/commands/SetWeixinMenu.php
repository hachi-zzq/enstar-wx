<?php

use Enstar\Library\Weixin\WeixinClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;


class SetWeixinMenu extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wx_menu:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Weixin menu.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $wxClient = new WeixinClient();
        $mqClient = new ReadMQ();
        $data_string = Config::get('weixin.menu');
        $rtJson = $wxClient->setMenu($data_string, $mqClient->getWeixinAccessToken());
        $rtJson = json_decode($rtJson);
        if ($rtJson and $rtJson->errcode != 0) {
            echo "Set Weixin menu error. \n";
            echo $rtJson->errmsg . "\n";
        }
        echo "Set Weixin menu complete. \n";
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
