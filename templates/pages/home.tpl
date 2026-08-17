{extends file='layouts/base.tpl'}

{block name='content'}
  {foreach $categories as $category}
    <section class="category-section">
      <div class="section-heading">
        <h2 id="category-{$category.id}">{$category.name|escape}</h2>
        <a class="section-heading__link" href="/category/{$category.slug|escape}">Все статьи</a>
      </div>

      <div class="post-grid">
        {foreach $category.posts as $post}
          {include file='partials/post-card.tpl' post=$post}
        {/foreach}
      </div>
    </section>
    {foreachelse}
    <p class="empty-state">Пока нет опубликованных статей.</p>
  {/foreach}
{/block}
