{extends file='layouts/base.tpl'}

{block name='content'}
  <article class="post">
    <header class="post__header">
      <div class="post__categories">
        {foreach $post.categories as $category}
          <a href="/category/{$category.slug|escape}">{$category.name|escape}</a>
        {/foreach}
      </div>
      <h1>{$post.title|escape}</h1>
      <p class="post__description">{$post.description|escape}</p>
      <div class="post__meta">
        <time datetime="{$post.published_at|date_format:'%Y-%m-%d'}">
          {$post.published_at|date_format:'%d.%m.%Y'}
        </time>
        <span>{$post.views} просмотров</span>
      </div>
    </header>

    {if $post.image}
      <img class="post__image" src="{$post.image|escape}" alt="" fetchpriority="high">
    {/if}

    <div class="post__content">{$post.content|escape|nl2br}</div>
  </article>

  {if $similar_posts}
    <section class="post">
      <div class="section-heading">
        <p class="eyebrow">Читайте также</p>
      </div>

      <div class="post-grid">
        {foreach $similar_posts as $post}
          {include file='partials/post-card.tpl' post=$post}
        {/foreach}
      </div>
    </section>
  {/if}
{/block}
