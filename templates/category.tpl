{extends file="layouts/base.tpl"}

{block name="content"}
    <p><a href="/">← На главную</a></p>

    <header>
        <h1>{$category.name|escape}</h1>
        <p>{$category.description|escape}</p>
        <p>Статей: {$totalArticles}</p>
    </header>

    <nav aria-label="Сортировка статей">
        Сортировать:
        {if $currentSort === 'date'}
            <strong>по дате</strong>
        {else}
            <a href="/categories/{$category.id}?sort=date">по дате</a>
        {/if}
        ·
        {if $currentSort === 'views'}
            <strong>по просмотрам</strong>
        {else}
            <a href="/categories/{$category.id}?sort=views">по просмотрам</a>
        {/if}
    </nav>

    <div>
        {foreach $articles as $article}
            <article>
                <a href="/articles/{$article.id}">
                    <img
                        src="{$article.imagePath|escape}"
                        alt=""
                        width="480"
                        height="270"
                    >
                    <h2>{$article.title|escape}</h2>
                </a>
                <p>{$article.description|escape}</p>
                <p>
                    <time datetime="{$article.publishedAt|escape}">
                        {$article.publishedAtLabel|escape}
                    </time>
                    · {$article.viewCount} просмотров
                </p>
            </article>
        {foreachelse}
            <p>В этой категории пока нет статей.</p>
        {/foreach}
    </div>

    {if count($pages) > 1}
        <nav aria-label="Пагинация">
            {foreach $pages as $page}
                {if $page.type === 'ellipsis'}
                    <span aria-hidden="true">…</span>
                {elseif $page.isCurrent}
                    <strong aria-current="page">{$page.number}</strong>
                {else}
                    <a href="{$page.url|escape}">{$page.number}</a>
                {/if}
            {/foreach}
        </nav>
    {/if}
{/block}
