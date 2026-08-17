<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AbeloHost Blog — статьи о разработке.">
    <title>{$page_title|escape}</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    {include file='partials/header.tpl'}
    <main class="container">
        {block name='content'}{/block}
    </main>
    {include file='partials/footer.tpl'}
</body>
</html>
