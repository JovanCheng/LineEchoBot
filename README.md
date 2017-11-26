# LineEchoBot
下載
<pre>
git clone https://github.com/DevinY/LineEchoBot.git line
</pre>

安裝及產生App Key
<pre>
cd line
composer install
cp .env.example .env
php artisan key:generate
</pre>

編輯.env檔，填入Line Console的資料
<pre>
LINEBOT_CHANNEL_ID=
LINEBOT_CHANNEL_SECRET=
LINEBOT_CHANNEL_TOKEN=
</pre>

預設Webhook URL Requires SSL(需使用https://)

https://您的域名/callback

訊息API推播測試
網址傳入: https://example.dev/api/push_text?message=這是推播訊息&to=[UserId]

查看Laravel的Log可以看到userId
<pre>
tail -f storage/logs/laravel.log 
</pre>
