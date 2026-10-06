{extends file="layouts/base.tpl"}

{block name="content"}
    <article class="article-detail">
        <a class="back-link" href="/">← На главную</a>

        <header class="article-detail__header">
            <p class="eyebrow">Статья</p>
            <h1>{$article.title|escape}</h1>
            <p class="article-detail__lead">{$article.description|escape}</p>
            <p class="article-meta">
                <time datetime="{$article.publishedAt|escape}">
                    {$article.publishedAtLabel|escape}
                </time>
                <span aria-hidden="true">·</span>
                <span>{$article.viewCount} просмотров</span>
            </p>
            <p class="tag-list">
                {foreach $article.categories as $category}
                    <a class="tag" href="/categories/{$category.id}">{$category.name|escape}</a>
                {/foreach}
            </p>
        </header>

        <img
            class="article-detail__image"
            src="{$article.imagePath|escape}"
            alt=""
            width="1200"
            height="675"
        >

        <div class="article-content">
            <p>{$article.content|escape}</p>
        </div>
    </article>

    {if $similarArticles}
        <aside class="similar-articles">
            <header class="section-heading">
                <div>
                    <p class="eyebrow">Читайте дальше</p>
                    <h2>Похожие статьи</h2>
                </div>
            </header>

            <div class="article-grid">
                {foreach $similarArticles as $similarArticle}
                    <article class="article-card">
                        <a class="article-card__image" href="/articles/{$similarArticle.id}" tabindex="-1">
                            <img
                                src="{$similarArticle.imagePath|escape}"
                                alt=""
                                width="480"
                                height="270"
                            >
                        </a>
                        <div class="article-card__body">
                            <h3>
                                <a href="/articles/{$similarArticle.id}">{$similarArticle.title|escape}</a>
                            </h3>
                            <p class="article-card__description">{$similarArticle.description|escape}</p>
                        </div>
                    </article>
                {/foreach}
            </div>
        </aside>
    {/if}
{/block}
