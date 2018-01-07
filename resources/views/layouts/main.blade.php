@php
    $config = [
        'debug' => DEBUG_MODE,
        'per_page' => PER_PAGE,
        'per_top_list' => PER_TOP_LIST
    ];
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    @if ($config['debug'])
        <title>test - DEBUG模式啟動中</title>
    @else
        <title>test</title>
    @endif

    <link href="css/main.css" rel="stylesheet" type="text/css"/>
    <link href="css/verticalScrollable.css" rel="stylesheet" type="text/css"/>
    <script language="javascript" src="js/jq/jquery-1.6.min.js"></script>
    <script language="javascript" src="js/jq/jquery.tools.min.js"></script>
    <script language="javascript" src="js/jq/jqFunction.js"></script>
    <script language="javascript" src="js/jq/jquery.corner.js"></script>


</head>
<body>
<div class="container">
    <div class="top">
        <div class="topLogo">
            logo
        </div>
        <form method="GET" action="index.php">
            <div class="topSearch">
                <input class="topSearchBar" type="text" name="val"/>
                <select name="query">
                    <option value="title">標題</option>
                    <option value="content">內容</option>
                </select>
                <input type="submit" value="搜尋"/>
                <input type="hidden" name="act" value="query"/>
                <input type="hidden" name="opera" value="like"/>
            </div>
        </form>
        <div class="topButton">
            @if ($config['debug'])
                <a href="admin.php">管理員頁面</a> |
            @endif
                <a href="/contact">聯絡我們</a> | <a
                    href="index.php?act=cart&amp;op=view">查看購物車</a> | <a href="index.php">回首頁</a>
        </div>
    </div>
    <div class="contentContainer">
        <div class="content">
            @yield('content')
        </div>
    </div>

    <div class="foot">
        <div class="recomend">
            <span style="color: #F00">Recomend</span><br/>
            1440 X 900+ <br/>
            InterExplorer 8 , FireFox 3 , Google Chrome +
        </div>
    </div>
</div>
</body>
