<?php
// إعدادات البوت
define('BOT_TOKEN', '8731473114:AAFFklxLcYm8VVNXfyzRQgz-MCpkUSaQrqE'); // غير هذا إلى التوكن حقك
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// قراءة البيانات من تيليجرام
$content = file_get_contents('php://input');
$update = json_decode($content, true);

if (!$update) {
    echo "IDLEB X Bot is Running";
    exit;
}

// معالجة الرسائل
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $message = $update['message']['text'] ?? '';
    
    if ($message == '/start') {
        sendMessage($chatId, "🎉 مرحباً بك في IDLEB X بوت\n\nالبوت الاحترافي لخدمات التواصل الاجتماعي\n\n@IDLEBX");
    } elseif ($message == '/services') {
        sendMessage($chatId, "📋 الخدمات المتوفرة:\n\n1. رشق مشاهدات انستقرام\n2. رشق لايكات انستقرام\n3. رشق حفظ انستقرام\n4. رشق مشاهدات ستوري\n5. رشق لايكات تيك توك\n6. رشق مشاهدات تيك توك");
    } else {
        sendMessage($chatId, "مرحباً! أرسل /services لمشاهدة الخدمات");
    }
}

function sendMessage($chatId, $text) {
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL . 'sendMessage');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
}
?>
