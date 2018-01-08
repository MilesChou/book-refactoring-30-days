@extends('admin.layout')

@section('sub_template')
    <div class="listBar">
        @if (isset($one))
            <div class="mainFrame">
                <form enctype="multipart/form-data" method="POST" name="shop"
                      action="/admin.php?act=shop&amp;op=upd&amp;id={{ $one['id'] }}">
                    <div class="mainContainer">
                        <div class="mainFrameImg">
                            @if ($one['pic'])
                                <img class="mainImg" src="img/{{ $one['pic'] }}"/>
                            @else
                                <img class="mainImg" src="img/000.png"/>
                            @endif
                        </div>
                        <div class="mainFrameHelp">
                            <p>標題：<input type="text" name="title" value="{{ $one['title'] }}"/></p>
                            <p>分類：
                                <select name="category">
                                    @foreach ($all_category as $category)
                                        @if ($category['id'] === $one['category'])
                                            <option value="{{ $category['id'] }}"
                                                    selected>{{ $category['title'] }}</option>
                                        @else
                                            <option value="{{ $category['id'] }}">{{ $category['title'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </p>
                            <p>成本：<input type="text" name="cost" value="{{ $one['cost'] }}"/></p>
                            <p>價格：<input type="text" name="price" value="{{ $one['price'] }}"/></p>
                            <p>庫存：<input type="text" name="store" value="{{ $one['store'] }}"/></p>
                            <p>銷售量：{{ $one['sale'] }}</p>
                            <p>點閱次數：{{ $one['click'] }}</p>
                            <p>照片：<input type="file" name="pic"/></p>
                        </div>
                    </div>
                    <div class="mainFrameContent">
                        <textarea name="content">{{ $one['content'] }}</textarea>
                    </div>
                    <div class="mainFrameFunction"><input type="submit" value="確認更改"/></div>
                </form>
            </div>
            <hr/>
        @else
            <div class="mainFrame">
                <form enctype="multipart/form-data" method="POST" name="shop" action="/admin.php?act=shop&amp;op=add">
                    <div class="mainContainer">
                        <div class="mainFrameHelp">
                            <p>標題：<input type="text" name="title"/></p>
                            <p>分類：
                                <select name="category">
                                    @foreach ($all_category as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['title'] }}</option>
                                    @endforeach
                                </select>
                            </p>
                            <p>成本：<input type="text" name="cost"/></p>
                            <p>價格：<input type="text" name="price"/></p>
                            <p>庫存：<input type="text" name="store"/></p>
                            <p>照片：<input type="file" name="pic"/></p>
                            <p>內容：<textarea name="content"></textarea></p>
                            <div><input type="submit" value="建立商品"/></div>
                        </div>
                    </div>
                </form>
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
                @isset ($all)
                    @foreach ($all as $product)
                        @if (0 === $loop->index % 5)
                            <div>
                                @endif
                                <div class="item">
                                    <div class="listImgFrame">
                                        @if ($product['pic'])
                                            <img class="listImg" src="/img/{{ $product['pic'] }}"/>
                                        @else
                                            <img class="listImg" src="/img/000.png"/>
                                        @endif
                                    </div>
                                    <table class="mainTable">
                                        <tr>
                                            <td>
                                                <div></div>
                                                <a href="/admin.php?act=shop&id={{ $product['id'] }}">{{ $product['title'] }}</a>
                                            </td>
                                            <td colspan="2">編號：{{ $product['id'] }}</td>
                                        </tr>
                                        <td>{{ $product['content'] }}</td>
                                        <td colspan="2">分類：
                                            <a href="/index.php?act=query&amp;query=category&amp;opera=eq&amp;val={{ $product['category'] }}">
                                                {{ $product['title'] }}
                                            </a>
                                        </td>
                                        <tr>
                                            <td></td>
                                            <td colspan="2">點閱次數：{{ $product['click'] }}</td>
                                        </tr>
                                        <td></td>
                                        <td colspan="2">庫存：{{ $product['store'] }}</td>
                                        <tr>
                                            <td></td>
                                            <td>成本：{{ $product['cost'] }}</td>
                                            <td>銷售量：{{ $product['sale'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Add Cart</div>
                                            </td>
                                            <td colspan="2">價格：{{ $product['price'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @if (0 === $loop->index % 5 || $loop->last)
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
    </div>
@endsection
