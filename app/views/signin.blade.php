<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enstar - Login</title>
    <link rel="stylesheet" href="/static/css/login.css">
</head>
<body>
<div class="page-wrapper">

    <div class="login-box">
        <div class="login-img">
            <div class="logo"><a href="/">EnStar</a></div>
            <div class="content">
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
            </div>
            <div class="copyright">
                <p>© 深圳英速達科技有限公司</p>
            </div>
        </div>
        <div class="login-form">
            <h2>用戶登入</h2>

            <form action="{{ route('signin_post') }}" method="post">
                <div class="group">
                    <input type="text" name="username" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>用戶名</label>
                </div>

                <div class="group">
                    <input type="password" name="password" required>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>密碼</label>
                    <a href="" class="forget-pw" title="忘記密碼">忘記密碼</a>
                </div>

                <button type="submit">登入</button>
            </form>

        </div>
    </div>
</div>
</body>
</html>