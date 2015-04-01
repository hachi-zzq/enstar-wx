<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 用户表 微信服务号
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('openid', 100)->comment('微信用户唯一标识');
            $table->smallInteger('subscribe',1)->default(0)->comment('是否订阅该公众号，0-未订阅');//用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
            $table->string('nickname', 32)->nullable()->comment('用户昵称');
            $table->smallInteger('sex',1)->default(0)->comment('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知');
            $table->string('province', 32)->nullable()->comment('用户个人资料填写的省份');
            $table->string('city', 32)->nullable()->comment('普通用户个人资料填写的城市');
            $table->string('country', 32)->nullable()->comment('国家，如中国为CN');
            $table->string('headimgurl', 256)->nullable()->comment('用户头像');//用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
            $table->string('privilege', 32)->nullable()->comment('用户特权信息，json 数组');//用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
            $table->string('unionid', 100)->nullable()->comment('身份标识 token');//只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            $table->smallInteger('status',1)->default(1)->comment('状态，0:冻结，1：正常');
            $table->string('language', 32)->nullable()->comment('用户的语言');
            $table->string('headimgurl', 256)->nullable()->comment('用户头像');//用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
            $table->string('privilege', 32)->nullable()->comment('用户特权信息，json 数组');//用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
            $table->string('unionid', 100)->nullable()->comment('身份标识 token');//只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            $table->smallInteger('status')->default(1)->comment('状态，0:冻结，1：正常');
            $table->timestamps();
            $table->softDeletes();
        });

        //admin管理员表
        Schema::create('admins', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('username', 100)->unique()->comment('账号');
            $table->string('password', 128)->comment('登陆密码');
            $table->smallInteger('status',1)->default(1)->comment('状态，0:冻结，1：正常');
            $table->string('ip', 15)->nullable()->comment('登陆ip');
            $table->string('remember_token', 100);
            $table->timestamps();
            $table->softDeletes();
        });

        // 用户 KV 表
        Schema::create('user_kv', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->string('key', 32)->nullable();
            $table->string('value')->nullable();
            $table->smallInteger('user_visible')->comment('用户可见性');
            $table->timestamps();
            $table->softDeletes();
        });


        // 课本表
        Schema::create('books', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('book_key', 5)->comment('课本标识符');
            $table->string('name')->comment('书名');
            $table->string('title')->comment('主标题');
            $table->string('subtitle')->comment('副标题');
            $table->string('description')->comment('描述');
            $table->string('cover')->comment('封面');
            $table->string('version', 10)->comment('版本');
            $table->string('book_unique', 100)->comment('唯一键，用于版本控制');
            $table->string('publisher')->nullable()->comment('出版社');
            $table->string('publish_time', 100)->nullable()->comment('出版时间');
            $table->string('tag', 100)->nullable()->comment('标签');
            $table->smallInteger('status')->default(0)->comment('状态，0:未发布，1:已发布');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
        });

        // 课本单元表
        Schema::create('units', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('unit_key', 10)->comment('单元标识，用于获取同一课文的不同发音版本');
            $table->string('name')->comment('单元名称');
            $table->integer('book_id')->comment('所属课本id');
            $table->integer('sort')->default(0)->comment('单元排序');
            $table->string('unit_unique', 100)->comment('唯一键，用于版本控制');
            $table->smallInteger('status')->default(0)->comment('状态，0:未发布，1:已发布');
            $table->timestamps();
            $table->softDeletes();
        });

        // 课文表
        Schema::create('lessons', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('guid', 36)->unique();
            $table->string('lesson_key', 10)->comment('课文标识，用于获取同一课文的不同发音版本');
            $table->string('language')->comment('课文语言'); // NceRocket
            $table->string('lesson_key', 10)->comment('课文标识，用于获取同一课文的不同发音版本'); // Enstar
            $table->string('language')->comment('课文语言'); // Enstar
            $table->string('title')->comment('标题');
            $table->text('raw_content')->comment('原文');
            $table->text('asr_content')->comment('ASR文本');
            $table->text('translation')->comment('译文'); // Enstar
            $table->string('audio')->comment('标准音');
            $table->decimal('duration', 10, 3)->nullable()->comment('时长');  //时长
            $table->integer('book_id')->comment('所属课本id');
            $table->integer('unit_id')->nullable()->comment('所属单元id');
            $table->smallInteger('sort')->comment('排序');
            $table->string('tag', 100)->comment('标签');
            $table->string('version', 100)->comment('版本号');
            $table->string('lesson_unique', 100)->comment('唯一键，用于版本控制');
            $table->smallInteger('status')->default(0)->comment('状态，0:未匹配、1正在匹配、2，匹配成功、-1匹配失败，-2，不可用');
            $table->decimal('asr_duration', 10, 3)->nullable()->comment('ASR匹配时间');
            $table->timestamps();
            $table->softDeletes();
        });

        // 课文报告表
        Schema::create('analyses', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('guid', 36)->unique();
            $table->integer('lesson_id')->comment('所属课文id');
            $table->smallInteger('source')->default(1)->comment('来源，1:云知声');
            $table->string('path')->comment('报告地址');
            $table->timestamps();
            $table->softDeletes();
        });

        // 句子表
        Schema::create('sentences', function ($table) {
            $table->increments('id')->comment('id');
            $table->integer('lesson_id')->comment('所属课文id');
            $table->text('raw_sentence')->comment('原始句子');
            $table->text('asr_sentence')->comment('ASR句子');
            $table->text('translation')->comment('翻译'); // Enstar
            $table->smallInteger('sort')->comment('句子排序');
            $table->string('prefix')->nullable()->comment('说话人');
            $table->string('format')->nullable()->comment('属性 L:换行,P:换段');
            $table->string('type')->comment('类型，对话、平常');
            $table->timestamps();
            $table->softDeletes();
        });

        // 阅读活动表
        Schema::create('reading', function ($table) {
            $table->increments('id')->comment('id');
            $table->integer('lesson_id')->comment('所属课文id');
            $table->string('lesson_key')->comment('课文标识');
            $table->string('language')->nullable()->comment('语言');
            $table->string('uuid')->nullable()->comment('uuid');
            $table->integer('audio_length')->nullable();
            $table->integer('user_id')->comment('用户id');
            $table->string('audio')->comment('录音');
            $table->decimal('duration', 10, 3)->nullable()->comment('音频长度');  //时常
            $table->integer('grade')->comment('星星'); // Enstar
            $table->integer('score')->comment('结果评级'); // Enstar
            $table->decimal('speed', 5, 2)->nullable()->comment('语速：每分钟单词数');
            $table->decimal('pronunciation_score', 5, 2)->nullable()->comment('发音得分');
            $table->decimal('intonation_score', 5, 2)->nullable()->comment('语调得分');
            $table->decimal('stress_score', 5, 2)->nullable()->comment('重音得分');
            $table->decimal('fluency_score', 5, 2)->nullable()->comment('流畅度得分');
            $table->decimal('speed_score', 5, 2)->nullable()->comment('语速得分');
            $table->decimal('completeness', 5, 2)->default(0)->comment('完成度');
            $table->string('speed_situation')->nullable()->comment('语速情况');
            $table->decimal('asr_duration', 5, 3)->nullable()->comment('ASR评测时间');  //asr分析时间

            $table->string('report')->comment('报告url');
            $table->smallInteger('status')->default(0)->comment('状态，-1:匹配失败 0:未分析 10:分析中 100:匹配完成');
            $table->timestamps();
            $table->softDeletes();
        });

        // 阅读报告表
        Schema::create('advisories', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('guid', 36)->unique();
            $table->integer('reading_id')->comment('所属阅读活动id');
            $table->smallInteger('source')->comment('来源，1:云知声');
            $table->string('path')->comment('报告地址');
            $table->timestamps();
            $table->softDeletes();
        });


        // 生词表
        Schema::create('new_words', function ($table) {
            $table->increments('id');
            $table->string('lesson_key')->comment('课文标识');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('word')->comment('单词内容');
            $table->string('en_pronunce')->comment('英式发音音标');
            $table->string('en_pronunce_url')->nullable()->comment('英式发音地址');
            $table->string('us_pronunce')->comment('美式发音');
            $table->string('us_pronunce_url')->nullable()->comment('美式发音地址');
            $table->string('property')->comment('词性');
            $table->string('translation')->comment('单词翻译');
            $table->smallInteger('status')->default(1)->comment('状态, 0删除; 1有效');
            $table->timestamps();
            $table->softDeletes();
        });

        // 字典表
        Schema::create('dictionary', function ($table) {
            $table->increments('id');
            $table->string('word')->index()->comment('单词内容');
            $table->string('en_pronunce')->comment('英式发音音标');
            $table->string('en_pronunce_url')->nullable()->comment('英式发音地址');
            $table->string('us_pronunce')->comment('美式发音');
            $table->string('us_pronunce_url')->nullable()->comment('美式发音地址');
            $table->text('property')->comment('词义，包含多个词性<-->词义的JSON序列化');
            $table->text('example_sentences')->nullable()->comment('例句，包含多个例句的JSON序列化');
            $table->smallInteger('status')->default(1)->comment('状态, 0删除; 1有效');
            $table->timestamps();
            $table->softDeletes();
        });

        // 用户单词表
        Schema::create('user_words', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('new_words_id')->nullable()->comment('生词表ID');
            $table->integer('dictionary_id')->nullable()->comment('字典表ID');
            $table->string('comment')->nullable()->comment('用户自定义的注释');
            $table->smallInteger('status')->default(1)->comment('状态, 0:删除; 1:新添加; 2:已掌握');
            $table->timestamps();
            $table->softDeletes();
        });

        // 意见反馈表
        Schema::create('feedback', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->comment('用户ID');
            $table->string('app_version')->nullable()->comment('应用版本号');
            $table->text('content')->comment('意见反馈内容');
            $table->string('contact')->nullable()->comment('联系方式');
            $table->smallInteger('status')->default(0)->comment('已读未读');
            $table->timestamps();
            $table->softDeletes();
        });

        // rest日志表
        Schema::create('rest_logs', function ($table) {
            $table->increments('id')->comment('id');
            $table->text('request')->nullable();
            $table->string('request_route')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('client_useragent')->nullable();
            $table->string('client_ip', 15);
            $table->string('msgcode', 6)->nullable();
            $table->text('message')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // redis 日志表
        Schema::create('redis_logs', function ($table) {
            $table->increments('id')->comment('id');
            $table->string('key')->comment('队列的 key');
            $table->text('content')->comment('提交进队列的内容');
            $table->timestamps();
            $table->softDeletes();
        });

        // 用户课文收藏列表
        Schema::create('user_favorites', function ($table) {
            $table->increments('id');
            $table->integer('lesson_id')->nullable()->comment('课文ID');
            $table->integer('user_id')->comment('用户ID');
            $table->timestamps();
        });

        // 用户课文分享列表
        Schema::create('user_share', function ($table) {
            $table->increments('id');
            $table->integer('lesson_id')->nullable()->comment('课文ID');
            $table->integer('user_id')->comment('用户ID');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
