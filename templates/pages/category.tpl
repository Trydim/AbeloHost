{extends file='layouts/base.tpl'}

{block name='content'}
  <section class="page-heading">
    <p class="eyebrow">Категория</p>
    <h1>{$category.name|escape}</h1>
    <p class="page-heading__lead">{$category.description|escape}</p>
  </section>

  <div class="toolbar">
    <p class="toolbar__label">Сортировать:</p>
    <div class="sort-links">
      <a class="sort-links__item{if $sort === 'latest'} is-active{/if}" href="?sort=latest">
        По дате публикации
      </a>
      <a class="sort-links__item{if $sort === 'popular'} is-active{/if}" href="?sort=popular">
        По просмотрам
      </a>
    </div>
  </div>

  {if $posts}
    <div class="post-grid">
      {foreach $posts as $post}
        {include file='partials/post-card.tpl' post=$post}
      {/foreach}
    </div>
  {else}
    <p class="empty-state">В этой категории пока нет статей.</p>
  {/if}

  {if $pagination.total_pages > 1}
    <nav class="pagination">
      {if $pagination.has_previous}
        <a class="pagination__item" href="?sort={$sort|escape}&amp;page={$pagination.current_page - 1}">←</a>
      {/if}
      {foreach $pagination.pages as $number}
        <a class="pagination__item{if $number === $pagination.current_page} is-active{/if}"
           href="?sort={$sort|escape}&amp;page={$number}"{if $number === $pagination.current_page} {/if}>{$number}</a>
      {/foreach}
      {if $pagination.has_next}
        <a class="pagination__item" href="?sort={$sort|escape}&amp;page={$pagination.current_page + 1}">→</a>
      {/if}
    </nav>
  {/if}
{/block}
