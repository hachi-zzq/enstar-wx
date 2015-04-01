<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no">
    <title>@section('title')@show- EnStar小英</title>
    <link rel="stylesheet" href="/assets/stylesheets/xiaoying.css"/>
    @section('css')

    @show
</head>
<body>
@section('content')


@show
<script>!function(e,t){function i(){var t=n.getBoundingClientRect().width;t/m>540&&(t=540*m);var i=t/10;n.style.fontSize=i+"px",d.rem=e.rem=i}var a,r=e.document,n=r.documentElement,o=r.querySelector('meta[name="viewport"]'),l=r.querySelector('meta[name="flexible"]'),m=0,s=0,d=t.flexible||(t.flexible={});if(o){console.warn("将根据已有的meta标签来设置缩放比例");var p=o.getAttribute("content").match(/initial\-scale=([\d\.]+)/);p&&(s=parseFloat(p[1]),m=parseInt(1/s))}else if(l){var c=l.getAttribute("content");if(c){var u=c.match(/initial\-dpr=([\d\.]+)/),f=c.match(/maximum\-dpr=([\d\.]+)/);u&&(m=parseFloat(u[1]),s=parseFloat((1/m).toFixed(2))),f&&(m=parseFloat(f[1]),s=parseFloat((1/m).toFixed(2)))}}if(!m&&!s){var v=(e.navigator.appVersion.match(/android/gi),e.navigator.appVersion.match(/iphone/gi)),h=e.devicePixelRatio;m=v?h>=3&&(!m||m>=3)?3:h>=2&&(!m||m>=2)?2:1:1,s=1/m}if(n.setAttribute("data-dpr",m),!o)if(o=r.createElement("meta"),o.setAttribute("name","viewport"),o.setAttribute("content","initial-scale="+s+", maximum-scale="+s+", minimum-scale="+s+", user-scalable=no"),n.firstElementChild)n.firstElementChild.appendChild(o);else{var x=r.createElement("div");x.appendChild(o),r.write(x.innerHTML)}e.addEventListener("resize",function(){clearTimeout(a),a=setTimeout(i,300)},!1),e.addEventListener("pageshow",function(e){e.persisted&&(clearTimeout(a),a=setTimeout(i,300))},!1),"complete"===r.readyState?r.body.style.fontSize=12*m+"px":r.addEventListener("DOMContentLoaded",function(){r.body.style.fontSize=12*m+"px"},!1),i(),d.dpr=e.dpr=m,d.refreshRem=i,d.rem2px=function(e){var t=parseFloat(e)*this.rem;return"string"==typeof e&&e.match(/rem$/)&&(t+="px"),t},d.px2rem=function(e){var t=parseFloat(e)/this.rem;return"string"==typeof e&&e.match(/px$/)&&(t+="rem"),t}}(window,window.lib||(window.lib={}));
    //# sourceMappingURL=/assets/scripts/flexible.js.map
</script>
<script type="text/javascript" src="/assets/scripts/wechat-1.0.0.js"></script>
<script>!function(t,n,a){a&&(t.weChatConfig=function(t){var e=n.querySelector("[data-wxsignature]").dataset,i={appId:e.appid,timestamp:parseInt(e.timestamp),nonceStr:e.noncestr,signature:e.signature,jsApiList:[]};for(var r in t)t.hasOwnProperty(r)&&(i[r]=t[r]);a.config(i)})}(window,document,window.wx);
    //# sourceMappingURL=/assets/scripts/wechatconfig.js.map
</script>
@section('js')

@show
</body>
</html>