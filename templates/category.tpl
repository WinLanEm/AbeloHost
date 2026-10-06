{extends file="layouts/base.tpl"}

{block name="content"}
    <a class="back-link" href="/">← На главную</a>

    <header class="page-header">
        <p class="eyebrow">Категория</p>
        <h1>{$category.name|escape}</h1>
        <p class="page-header__lead">{$category.description|escape}</p>

        <div id="articles" class="category-toolbar">
            <p class="article-count">Статей: {$totalArticles}</p>

            <nav class="sort-bar" aria-label="Сортировка статей">
                <span class="sort-bar__label">Сортировать:</span>
                <div class="sort-bar__options">
                    {if $currentSort === 'date'}
                        <strong aria-current="true">По дате</strong>
                    {else}
                        <a href="/categories/{$category.id}?sort=date#articles">По дате</a>
                    {/if}
                    {if $currentSort === 'views'}
                        <strong aria-current="true">По просмотрам</strong>
                    {else}
                        <a href="/categories/{$category.id}?sort=views#articles">По просмотрам</a>
                    {/if}
                </div>
            </nav>
        </div>
    </header>

    <div class="article-grid article-grid--listing">
        {foreach $articles as $article}
            <article class="article-card">
                <a class="article-card__image" href="/articles/{$article.id}" tabindex="-1">
                    <img
                        src="{$article.imagePath|escape}"
                        alt=""
                        width="480"
                        height="270"
                    >
                </a>
                <div class="article-card__body">
                    <h2><a href="/articles/{$article.id}">{$article.title|escape}</a></h2>
                    <p class="article-card__description">{$article.description|escape}</p>
                    <p class="article-meta">
                        <time datetime="{$article.publishedAt|escape}">
                            {$article.publishedAtLabel|escape}
                        </time>
                        <span aria-hidden="true">·</span>
                        <span>{$article.viewCount} просмотров</span>
                    </p>
                </div>
            </article>
        {foreachelse}
            <p class="empty-state">В этой категории пока нет статей.</p>
        {/foreach}
    </div>

    {if count($pages) > 1}
        <nav class="pagination" aria-label="Пагинация">
            {foreach $pages as $page}
                {if $page.type === 'ellipsis'}
                    <span class="pagination__ellipsis" aria-hidden="true">…</span>
                {elseif $page.isCurrent}
                    <strong aria-current="page">{$page.number}</strong>
                {else}
                    <a href="{$page.url|escape}#articles">{$page.number}</a>
                {/if}
            {/foreach}
        </nav>
    {/if}
{/block}
