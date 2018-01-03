# 看到 code 寫成這樣我也是醉了，不如試試重構？

有過慘痛維護經驗的開發者都會了解，程式是需要設計的！設計不良的架構，會在未來增修功能的時候，大喊要殺了某人；但追求完美設計的下場，反而會被不懂程式的非工程人員追進度，還會被嫌沒效率；「重構」能在這兩個極端之間取得一個平衡。它能在具備基本設計的架構上，持續以增修功能為目的，補足設計上的缺陷。不僅能持續交付程式碼，也能持續改善設計，好重構，不試嗎？

## 前言

雖然我看過的程式不是很多，但犯過的蠢事相信絕對不少，至少有寫過無數難以維護的程式碼。而當我在新增功能不順利的時候，最想殺的那個人，通常都是數個月前的自己。

為了不讓未來的自己起殺意，於是開始學設計模式、重構、單元測試、[持續整合][]、 DevOps …等。學習與分享不為別的，除了求其他人（包括未來的自己）不要殺我之外，也真心希望大家能帶著愉快的心情，開發出真正有價值的軟體。

現在，雖然骯髒的程式碼已經寫下去了，但軟體是軟的，還回得去。重構正是其中一個有用的技巧，可以讓原本的殺意降低，愉快的心情增加。除此之外，還能提高程式碼的穩定度，讓大家對程式碼更有信心，也對部署更放心。

未來 30 天，將會分享我對於重構的了解，以及示範如何做重構，希望大家對重構能有更深刻的認識。

[持續整合]: https://github.com/MilesChou/book-intro-of-ci

## 目錄

* [Day 1 - 什麼是重構（Refactoring）](/docs/day01.md)
* [Day 2 - 技術債（Technical Debt）](/docs/day02.md)
* [Day 3 - 非技術人員所要了解的警訊](/docs/day03.md)
* [Day 4 - 開發者能察覺的壞味道（Bad Smell）](/docs/day04.md)
* [Day 5 - 敏捷與重構](/docs/day05.md)
* [Day 6 - 軟體量測（Software Metric）](/docs/day06.md)
* [Day 7 - SOLID 之 單一職責原則（Single responsibility principle）](/docs/day07.md)
* [Day 8 - SOLID 之 開關原則（Open-close principle）](/docs/day08.md)
* [Day 9 - SOLID 之 里氏替換原則（Liskov substitution principle）](/docs/day09.md)
* [Day 10 - SOLID 之 介面隔離原則（Interface segregation principle）](/docs/day10.md)
* [Day 11 - SOLID 之 依賴反轉原則（Dependency inversion principle）](/docs/day11.md)
* [Day 12 - 不在 SOLID 裡的 最小知識原則（Least Knowledge Principle）](/docs/day12.md)
* [Day 13 - 看到 code 寫成這樣我也是醉了](/docs/day13.md)
* [Day 14 - 重構的第一步－－讓程式可以動](/docs/day14.md)
* [Day 15 - 來試著升級 PHP 吧](/docs/day15.md)
* [Day 16 - 導入 Composer](/docs/day16.md)
* [Day 17 - 整合 Laravel](/docs/day17.md)
* [Day 18 - 導入 Database Migration](/docs/day18.md)
* [Day 19 - 整合 CI](/docs/day19.md)

## 誌謝

* 感謝老婆為了支持我寫作，把家裡打點好好的，也感謝老婆幫我看文章。
* 互相鼓（ㄕㄤ）勵（ㄏㄞˋ）的團隊成員 [聖佑](https://github.com/shengyou) 與 [Scott](https://github.com/shazi7804)
* 幫忙看文章的 [@pexlkw](https://github.com/pexlkw) 與 [@phoebe90](https://github.com/phoebe90)
* 互相推坑的 [DevOps Taiwan](https://www.facebook.com/groups/DevOpsTaiwan/) 夥伴們
