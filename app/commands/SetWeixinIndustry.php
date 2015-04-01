<?php

use Enstar\Library\Weixin\WeixinClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class SetWeixinIndustry extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wx_industry:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Weixin industry.';

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
        $mqClient = new ReadMQ();
        $wxClient = new WeixinClient();
        $data_string = Config::get('weixin.industry');
        $rtJson = $wxClient->setIndustry($data_string, $mqClient->getWeixinAccessToken());
        $rtJson = json_decode($rtJson);
        if ($rtJson and $rtJson->errcode != 0) {
            echo "Set Weixin industry error. \n";
            echo $rtJson->errmsg . "\n";
        }
        echo "Set Weixin industry complete. \n";

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
