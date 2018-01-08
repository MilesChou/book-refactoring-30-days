@extends('layouts.main')

@section('content')
    <div class="adminClassBar">
        <div class="classBar">
            <div class="blockBottomLine">
                管理功能
            </div>
            <ul>
                <li><a href="/admin.php?act=shop&amp;op=view">商品管理</a></li>
                <li><a href="/admin.php?act=order&amp;op=view">訂單管理</a></li>
            </ul>
        </div>
        @isset($all_category)
            <div class="classBar">
                <div class="blockBottomLine">
                    分類
                </div>
                <ul>
                    @foreach ($all_category as $category)
                        <li>
                            <a href="admin.php?act=shop&amp;op=query&amp;query=category&amp;opera=eq&amp;val={{ $category['id'] }}">
                                {{ $category['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endisset
    </div>
    <div class="adminContent">
        <div class="listBar">
            @yield('sub_template')
        </div>
    </div>
@endsection