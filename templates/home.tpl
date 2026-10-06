{extends file="layouts/base.tpl"}

{block name="content"}
    <h1 class="visually-hidden">{$pageTitle|escape}</h1>

    {foreach $categories as $category}
        <section class="category-section">
            <header class="section-heading">
                <div>
                    <p class="eyebrow">Категория</p>
                    <h2>{$category.name|escape}</h2>
                    <p>{$category.description|escape}</p>
                </div>
                <a class="button button--secondary" href="/categories/{$category.id}">Все статьи</a>
            </header>

            <div class="article-grid">
                {foreach $category.articles as $article}
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
                            <h3>
                                <a href="/articles/{$article.id}">{$article.title|escape}</a>
                            </h3>
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
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p class="empty-state">Статьи пока не опубликованы.</p>
    {/foreach}
{/block}
