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
        {if $headingLevel === 2}
            <h2><a href="/articles/{$article.id}">{$article.title|escape}</a></h2>
        {else}
            <h3><a href="/articles/{$article.id}">{$article.title|escape}</a></h3>
        {/if}
        <p class="article-card__description">{$article.description|escape}</p>
        {if $showMeta}
            <p class="article-meta">
                <time datetime="{$article.publishedAt|escape}">
                    {$article.publishedAtLabel|escape}
                </time>
                <span aria-hidden="true">·</span>
                <span>{$article.viewCount} просмотров</span>
            </p>
        {/if}
    </div>
</article>
