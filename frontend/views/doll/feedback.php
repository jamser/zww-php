<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>反馈信息</title>
    <link rel="stylesheet" type="text/css" href="./products/css/reset.css"/>

    <script type="text/javascript" src="./products/js/setfontsize.js">
    </script>
</head>

<body>
<div class="wrap">
    <section class="Feedback">

        <div class="Feedback_category">
					<textarea rows="8" cols="8">

					</textarea>
        </div>
        <div class="Feedback_content">
					<textarea rows="8" cols="8">

					</textarea>
        </div>
        <div class="Confirmation_sublimt">
            <input type="submit" value="- 确 认 提 交  -" />
            <!--<a href="javascript:void(0)">- 确 认 提 交 -</a>-->
        </div>

    </section>

</div>

</body>
<style type="text/css">
    .wrap {
        width: 100%;
        height: 12.08rem;
        background: url("./products/img/Common/Common_BgImage_Top.png") no-repeat;
        background-size: 100% 100%;
    }

    .Feedback {
        width: 5.9rem;
        height: 9.75rem;
        position: absolute;
        top: 1.75rem;
        left: 0.25rem;
    }

    .Feedback_category {
        width: 100%;
        height: 4.05rem;
        background: url("./products/img/Setup/Setup_BgImage_FeedType.png")no-repeat;
        background-size: 100% 3.85rem;
        position: relative;
    }

    .Feedback_category textarea {
        display: block;
        width: 5.1rem;
        height: 2.3rem;
        outline: none;
        margin: 0 auto;
        position: absolute;
        top: 1.3rem;
        left: 0.4rem;
        border: none;
        background: none;
    }

    .Feedback_content {
        width: 100%;
        height: 5.05rem;
        background: url("./products/img/Setup/Setup_BgImage_FeedText.png")no-repeat;
        background-size: 100% 4.6rem;
        position: relative;
    }

    .Feedback_content textarea {
        display: block;
        width: 5.1rem;
        height: 2.3rem;
        outline: none;
        margin: 0 auto;
        position: absolute;
        top: 1.3rem;
        left: 0.4rem;
        border: none;
        background: none;
    }

    .Confirmation_sublimt {
        width: 100%;
        height: 0.7rem;
    }

    .Confirmation_sublimt input {
        display: block;
        width: 100%;
        height: 0.7rem;
        background: url("./products/img/Mine/Mine_BgImage_Red.png") no-repeat;
        background-size: 100% 0.65rem;
        text-align: center;
        font-size: 0.3rem;
        line-height: 0.7rem;
        color: white;
        border: none;
        outline: none;
    }
</style>

</html>