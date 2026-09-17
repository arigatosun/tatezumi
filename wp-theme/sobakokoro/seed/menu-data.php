<?php
/**
 * お品書きの初期データ（静的HTML版 pattern-d の内容をそのまま移したもの）
 *
 * 価格の出典: 看板用_0810.pdf（店内掲示・2026年8月）。
 * 掲示になかった品は食べログのメニュー写真由来なので、公開前に店主確認が必要。
 *
 * cat   … 区分のスラッグ
 * title … 品名
 * price … 表示したいままの価格
 * note  … 品名の下の小さな注記
 * badge … limited / takeout / summer / winter
 * photo … assets/img/photo/ のファイル名（空ならカードではなく価格表に出る）
 */

return [
    // 温かいお蕎麦
    ['cat' => 'hot', 'title' => '天そば',       'price' => '1,650円', 'photo' => 'tensoba.jpg'],
    ['cat' => 'hot', 'title' => '鴨南蛮',       'price' => '1,650円', 'photo' => 'kamo-nanban.jpg'],
    ['cat' => 'hot', 'title' => 'ねぎそば',     'price' => '1,000円', 'photo' => 'negisoba.jpg'],
    ['cat' => 'hot', 'title' => 'おろし肉そば', 'price' => '1,000円', 'photo' => 'oroshi-niku-soba.jpg'],

    // 冷たいお蕎麦
    ['cat' => 'cold', 'title' => '天ざる',         'price' => '1,600円', 'photo' => 'tenzaru.jpg'],
    ['cat' => 'cold', 'title' => 'ざる',           'price' => '900円'],
    ['cat' => 'cold', 'title' => 'おろしそば',     'price' => '1,100円'],
    ['cat' => 'cold', 'title' => 'とろろそば',     'price' => '1,100円'],
    ['cat' => 'cold', 'title' => 'とり天ぶっかけ', 'price' => '1,350円'],
    ['cat' => 'cold', 'title' => 'かも汁',         'price' => '1,700円', 'note' => '紀州鴨使用', 'badge' => 'limited'],

    // トッピング・大盛り
    ['cat' => 'topping', 'title' => '各種そば 大盛り', 'price' => '+300円'],
    ['cat' => 'topping', 'title' => '温泉卵のせ',      'price' => '+100円'],
    ['cat' => 'topping', 'title' => '白ネギ',          'price' => '+50円'],
    ['cat' => 'topping', 'title' => 'おろし',          'price' => '+50円'],
    ['cat' => 'topping', 'title' => '天かす',          'price' => '無料'],

    // ミニ丼
    ['cat' => 'donburi', 'title' => '天丼',       'price' => '550円', 'photo' => 'tendon.jpg'],
    ['cat' => 'donburi', 'title' => '牛丼',       'price' => '550円', 'photo' => 'gyudon.jpg'],
    ['cat' => 'donburi', 'title' => 'ネギトロ丼', 'price' => '550円', 'photo' => 'negitoro-don.jpg'],

    // ちょっと一品（写真のある2品を先に並べる）
    ['cat' => 'side', 'title' => 'とり天（5個）',     'price' => '450円', 'badge' => 'takeout', 'photo' => 'toriten.jpg'],
    ['cat' => 'side', 'title' => '揚出し豆腐',        'price' => '400円', 'photo' => 'agedashi-tofu.jpg'],
    ['cat' => 'side', 'title' => 'とり天（2個）',     'price' => '300円', 'badge' => 'takeout'],
    ['cat' => 'side', 'title' => '天ぷら盛り合わせ',  'price' => '900円', 'badge' => 'takeout', 'note' => '大海老二尾・南瓜・ししとう・海苔'],
    ['cat' => 'side', 'title' => '大海老天（一尾）',  'price' => '350円', 'badge' => 'takeout'],
    ['cat' => 'side', 'title' => 'だし巻き玉子',      'price' => '300円'],

    // お飲み物
    ['cat' => 'drink', 'title' => '生ビール',   'price' => '500円',   'note' => 'アサヒ生ビール マルエフ'],
    ['cat' => 'drink', 'title' => '瓶ビール',   'price' => '650円',   'note' => 'スーパードライ（中瓶）'],
    ['cat' => 'drink', 'title' => 'チューハイ', 'price' => '各450円', 'note' => 'プレーン／レモン'],
    ['cat' => 'drink', 'title' => 'ハイボール', 'price' => '450円',   'note' => 'ブラックニッカ'],
    ['cat' => 'drink', 'title' => '焼酎',       'price' => '各450円', 'note' => '麦：二階堂／芋：黒霧島（ロック・水割り・お湯割り）'],
    ['cat' => 'drink', 'title' => '日本酒',     'price' => '900円',   'note' => '香住鶴（冷酒）'],
    ['cat' => 'drink', 'title' => 'ドライゼロ', 'price' => '400円',   'note' => 'ノンアルコールビール'],

    // 季節のおすすめ
    ['cat' => 'seasonal', 'title' => '海老天ぶっかけ',     'price' => '1,350円', 'badge' => 'summer'],
    ['cat' => 'seasonal', 'title' => '梅おろしそば（冷）', 'price' => '1,300円', 'badge' => 'summer'],
];
