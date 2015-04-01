<xml>
    <ToUserName><![CDATA[{{ $message->ToUserName}}]]></ToUserName>
    <FromUserName><![CDATA[{{ $message->FromUserName}}]]></FromUserName>
    <CreateTime>{{ time() }}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{{ $message->Content }}]]></Content>
</xml>