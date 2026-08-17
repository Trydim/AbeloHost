<article class="post-card">
  <a class="post-card__image-link" href="/post/{$post.slug|escape}">
    {if $post.image}
      <img class="post-card__image" loading="lazy" alt="" src="{$post.image|escape}">
    {else}
      <div class="post-card__image placeholder"></div>
    {/if}
  </a>
  <div class="post-card__body">
    <h3 class="post-card__title">
      <a href="/post/{$post.slug|escape}">{$post.title|escape}</a>
    </h3>
    <div class="post-card__meta">
      <time datetime="{$post.published_at|date_format:'%Y-%m-%d'}">
        {$post.published_at|date_format:'%d.%m.%Y'}
      </time>
      <span>{$post.views} просмотров</span>
    </div>
    <p class="post-card__description">
      {$post.description|escape}

      <a href="/post/{$post.slug|escape}" class="post-card__read-more">Продолжить чтение</a>
    </p>
  </div>
</article>
