<xml>
    <ToUserName><![CDATA[{{ $message->ToUserName}}]]></ToUserName>
    <FromUserName><![CDATA[{{ $message->FromUserName }}]]></FromUserName>
    <CreateTime>{{ time() }}</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>{{count($message->items)}}</ArticleCount>
    <Articles>
        @foreach($message->items as $item)
            <item>
                <Title><![CDATA[{{$item->title}}]]></Title>
                <Description><![CDATA[{{$item->description}}]]></Description>
                <PicUrl><![CDATA[{{$item->picUrl}}]]></PicUrl>
                <Url><![CDATA[{{$item->url}}]]></Url>
            </item>
        @endforeach
    </Articles>
</xml>