@extends('layouts.main')

@section('content')
    <div class="classBar">
        <div class="blockBottomLine">
            產品分類
        </div>
        <ul>
            @foreach ($all_category as $category)
                <li>
                    <a href="index.php?act=query&amp;query=category&amp;opera=eq&amp;val={{ $category['id'] }}">{{ $category['title'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="listBar">
        @if (isset($one))
            <div class="mainFrame">
                <div class="mainContainer">
                    <div class="mainFrameImg">
                        @if ($one['pic'])
                            <img class="mainImg" src="img/{{ $one['pic'] }}"/>
                        @else
                            <img class="mainImg" src="img/000.png"/>
                        @endif
                    </div>
                    <div class="mainFrameHelp">
                        標題：{{ $one['title'] }}
                        <p>編號：{{ $one['id'] }}</p>
                        <p>分類：{{ $one['title'] }}</p>
                        <p>價格：{{ $one['price'] }}</p>
                        <p>銷售量：{{ $one['sale'] }}</p>
                        <p>點閱：{{ $one['click'] }}</p>
                    </div>
                </div>
                <div class="mainFrameContent">
                    {{ $one['content'] }}
                </div>
                <div class="mainFrameFunction">
                    @if (0 === $one['store'])
                        目前無庫存
                    @else
                        <a href="index.php?act=cart&amp;op=add&amp;id={{ $one['id'] }}">加入購物車</a>
                    @endif
                </div>
            </div>
            <hr/>
        @endif

        <div id="actions">
            <div class="widthBar">
                <a class="prev">&laquo; 上一頁</a>
                <a class="next">下一頁 &raquo;</a>
            </div>
        </div>
        <div class="scrollable vertical">

            <div class="items">
                @isset($all)
                    @foreach ($all as $shop)
                        @if (0 === $loop->index % 4)
                            <div>
                                @endif
                                <div class="item">
                                    <div class="listImgFrame">
                                        @if (isset($shop['pic']))
                                            <img class="listImg"
                                                 src="img/{{ $shop['pic'] }}"/>
                                        @else
                                            <img class="listImg"
                                                 src="img/000.png"/>
                                        @endif
                                    </div>
                                    <table class="mainTable">
                                        <tr>
                                            <td>
                                                <div class="titleLink">
                                                    @if ($act === 'query')
                                                        <a href="index.php?act=query&amp;query={{ $act }}&amp;opera={{ $opera }}&amp;val={{ $val }}&amp;id={{ $shop['id'] }}">{{ $shop['title'] }}</a>
                                                    @else
                                                        <a href="index.php?id={{ $shop['id'] }}">{{ $shop['title'] }}</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>編號：{{ $shop['id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ $shop['content'] }}</td>
                                            <td>
                                                分類：<a href="index.php?act=query&amp;query=category&amp;opera=eq&amp;val={{ $shop['category'] }}">{{ $shop['title'] }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>點閱：{{ $shop['click'] }}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>庫存：{{ $shop['store'] }}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>銷售量：{{ $shop['sale'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                @if (0 === $shop['store'])
                                                    目前無庫存
                                                @else
                                                    <a href="index.php?act=cart&amp;op=add&amp;id={{ $shop['id'] }}">加入購物車</a>
                                                @endif
                                            </td>
                                            <td>價格：{{ $shop['price'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @if (0 === $loop->index % 4 || $loop->last)
                            </div>
                        @endif
                    @endforeach
                @endisset
            </div>
        </div>
        <div id="actions">
            <div class="widthBar">
                <a class="prev">&laquo; 上一頁</a>
                <a class="next">下一頁 &raquo;</a>
            </div>
        </div>
        <div id="actions">
            <div class="fixedBar">
                <a class="prev">上一頁</a>
                <a class="next">下一頁</a>
            </div>
        </div>
    </div>
@endsection
