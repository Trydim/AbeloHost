<?php

declare(strict_types=1);

use Kernel\DB\Database;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$pdo = Database::connect();

$categories = [
  'development' => [
    'name' => 'Разработка',
    'slug' => 'development',
    'description' => 'Практики разработки, архитектура приложений и работа с кодом.',
  ],
  'devops' => [
    'name' => 'Инфраструктура',
    'slug' => 'infrastructure',
    'description' => 'Docker, CI/CD, базы данных и надёжная эксплуатация сервисов.',
  ],
  'design' => [
    'name' => 'Дизайн',
    'slug' => 'design',
    'description' => 'Интерфейсы, визуальная иерархия и продуктовый дизайн.',
  ],
  'business' => [
    'name' => 'Продукт',
    'slug' => 'product',
    'description' => 'Работа с задачами, метриками и развитием цифровых продуктов.',
  ],
];

$posts = [
  [
    'title' => 'Как начать проект на чистом PHP',
    'slug' => 'kak-nachat-proekt-na-chistom-php',
    'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Минимальная структура проекта, которая не мешает расти приложению.',
    'content' => <<<'TEXT'
Чистый PHP не означает хаотичный набор файлов. Начните с единой точки входа в public, отдельного каталога для исходного кода и конфигурации окружения.

Сразу определите границы между HTTP-слоем, доступом к данным и шаблонами. Такая структура остаётся простой, но позволяет безболезненно добавлять новые страницы и команды.
TEXT,
    'views' => 482,
    'published_at' => '2026-08-14 09:00:00',
    'categories' => ['development', 'devops'],
  ],
  [
    'title' => 'Компонентный подход к интерфейсу',
    'slug' => 'komponentnyy-podhod-k-interfeysu',
    'image' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Как сделать шаблоны повторно используемыми и не потерять ясность.',
    'content' => <<<'TEXT'
Повторяющиеся блоки интерфейса лучше оформить как самостоятельные шаблоны. Карточка статьи, навигация и кнопка должны иметь предсказуемый входной набор данных.

Компоненты снижают количество копирования и помогают поддерживать единый визуальный язык на всех страницах сайта.
TEXT,
    'views' => 327,
    'published_at' => '2026-08-13 11:30:00',
    'categories' => ['development', 'design'],
  ],
  [
    'title' => 'Зачем проекту локальное Docker-окружение',
    'slug' => 'zachem-proektu-lokalnoe-docker-okruzhenie',
    'image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Одинаковые версии сервисов для команды и быстрый старт нового разработчика.',
    'content' => <<<'TEXT'
Контейнеры описывают рабочее окружение рядом с кодом. Разработчик получает те же версии PHP и MySQL, что используются у остальных участников команды.

Важно хранить в compose только инфраструктуру приложения и не включать в образ секреты или пользовательские данные.
TEXT,
    'views' => 891,
    'published_at' => '2026-08-12 15:15:00',
    'categories' => ['devops', 'business'],
  ],
  [
    'title' => 'Визуальная иерархия в карточке статьи',
    'slug' => 'vizualnaya-ierarhiya-v-kartochke-stati',
    'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Расставляем акценты с помощью размера, отступов и контраста.',
    'content' => <<<'TEXT'
У карточки есть одна главная задача — быстро сообщить, почему статью стоит открыть. Заголовок должен быть самым заметным элементом, а описание — помогать принять решение.

Ограничьте число акцентов. Если одинаково яркими сделать дату, метки и кнопку, пользователь перестанет видеть главное.
TEXT,
    'views' => 265,
    'published_at' => '2026-08-11 10:00:00',
    'categories' => ['design'],
  ],
  [
    'title' => 'Полезные индексы MySQL для блога',
    'slug' => 'poleznye-indeksy-mysql-dlya-bloga',
    'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Какие поля индексировать для публикации, сортировки и выборки статей.',
    'content' => <<<'TEXT'
Индекс нужен там, где данные регулярно фильтруются или сортируются. Для блога это обычно дата публикации, количество просмотров и внешние ключи таблицы связей.

Не стоит индексировать каждую колонку. Лишние индексы замедляют вставку и обновление записей.
TEXT,
    'views' => 714,
    'published_at' => '2026-08-10 13:45:00',
    'categories' => ['development', 'devops'],
  ],
  [
    'title' => 'Метрики, которые помогают развивать блог',
    'slug' => 'metriki-kotorye-pomogayut-razvivat-blog',
    'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Смотрим не только на просмотры, но и на качество чтения.',
    'content' => <<<'TEXT'
Количество просмотров показывает интерес, но не объясняет пользу материала. Дополняйте его глубиной прокрутки, переходами к связанным статьям и возвратами читателей.

Метрики работают, когда перед их сбором есть вопрос. Например: помогает ли новый формат быстрее находить нужную тему?
TEXT,
    'views' => 193,
    'published_at' => '2026-08-09 09:20:00',
    'categories' => ['business'],
  ],
  [
    'title' => 'Доступный контраст без потери стиля',
    'slug' => 'dostupnyy-kontrast-bez-poteri-stilya',
    'image' => 'https://images.unsplash.com/photo-1545235617-9465d2a55698?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Проверяем текст, состояния кнопок и вспомогательные элементы интерфейса.',
    'content' => <<<'TEXT'
Контраст влияет не только на доступность, но и на скорость восприятия интерфейса. Второстепенный текст может быть спокойнее основного, однако он должен оставаться читаемым.

Проверяйте состояния наведения и фокуса отдельно: именно в них часто исчезает нужная разница между элементами.
TEXT,
    'views' => 408,
    'published_at' => '2026-08-08 16:10:00',
    'categories' => ['design', 'business'],
  ],
  [
    'title' => 'Подготовка релиза без лишнего стресса',
    'slug' => 'podgotovka-reliza-bez-lishnego-stressa',
    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Небольшой чек-лист для команды перед публикацией изменений.',
    'content' => <<<'TEXT'
Хороший релиз начинается не в день публикации. Определите ответственного, подготовьте план отката и заранее согласуйте, какие изменения увидит пользователь.

Короткий повторяемый чек-лист надёжнее длинного документа, который открывают только при аварии.
TEXT,
    'views' => 556,
    'published_at' => '2026-08-07 12:00:00',
    'categories' => ['development', 'business'],
  ],
  [
    'title' => 'Резервные копии: что проверять кроме расписания',
    'slug' => 'rezervnye-kopii-chto-proveryat-krome-raspisaniya',
    'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1200&q=80',
    'description' => 'Бэкап полезен только тогда, когда данные из него можно восстановить.',
    'content' => <<<'TEXT'
Автоматическое создание резервных копий — лишь половина задачи. Регулярно проверяйте восстановление в изолированном окружении и фиксируйте время, которое занимает процесс.

Храните копии отдельно от рабочего сервера и ограничивайте доступ к ним так же строго, как к основной базе.
TEXT,
    'views' => 639,
    'published_at' => '2026-08-06 14:30:00',
    'categories' => ['devops'],
  ],
];

$categoryStatement = $pdo->prepare(
  'INSERT INTO categories (name, slug, description)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE
        name = ?, description = ?, id = LAST_INSERT_ID(id), updated_at = CURRENT_TIMESTAMP',
);

$postStatement = $pdo->prepare(
  'INSERT INTO posts (title, slug, image, description, content, views, published_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        title = ?, image = ?, description = ?, content = ?, published_at = ?,
        id = LAST_INSERT_ID(id), updated_at = CURRENT_TIMESTAMP',
);

$postCategoryStatement = $pdo->prepare(
  'INSERT IGNORE INTO post_categories (post_id, category_id) VALUES (?, ?)',
);

try {
  $pdo->beginTransaction();
  $categoryIds = [];

  foreach ($categories as $key => $category) {
    $categoryStatement->execute([
      $category['name'],
      $category['slug'],
      $category['description'],
      $category['name'],
      $category['description'],
    ]);
    $categoryIds[$key] = (int) $pdo->lastInsertId();
  }

  foreach ($posts as $post) {
    $postStatement->execute([
      $post['title'],
      $post['slug'],
      $post['image'],
      $post['description'],
      $post['content'],
      $post['views'],
      $post['published_at'],
      $post['title'],
      $post['image'],
      $post['description'],
      $post['content'],
      $post['published_at'],
    ]);
    $postId = (int) $pdo->lastInsertId();

    foreach ($post['categories'] as $category) {
      $postCategoryStatement->execute([$postId, $categoryIds[$category]]);
    }
  }

  $pdo->commit();
} catch (Throwable $exception) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }

  throw $exception;
}

echo sprintf("Сидирование завершено: %d категорий, %d статей.\n", count($categories), count($posts));
