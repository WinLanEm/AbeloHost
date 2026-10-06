{extends file="layouts/base.tpl"}

{block name="content"}
    <p><a href="/">← На главную</a></p>

    <article>
        <header>
            <h1>{$article.title|escape}</h1>
            <p>{$article.description|escape}</p>
            <p>
                <time datetime="{$article.publishedAt|escape}">
                    {$article.publishedAtLabel|escape}
                </time>
                · {$article.viewCount} просмотров
            </p>
            <p>
                {foreach $article.categories as $category}
                    <a href="/categories/{$category.id}">{$category.name|escape}</a>{if !$category@last}, {/if}
                {/foreach}
            </p>
        </header>

        <img
            src="{$article.imagePath|escape}"
            alt=""
            width="1200"
            height="675"
        >

        <p>{$article.content|escape}</p>
    </article>

    {if $similarArticles}
        <aside>
            <h2>Похожие статьи</h2>

            <div>
                {foreach $similarArticles as $similarArticle}
                    <article>
                        <a href="/articles/{$similarArticle.id}">
                            <img
                                src="{$similarArticle.imagePath|escape}"
                                alt=""
                                width="480"
                                height="270"
                            >
                            <h3>{$similarArticle.title|escape}</h3>
                        </a>
                        <p>{$similarArticle.description|escape}</p>
                    </article>
                {/foreach}
            </div>
        </aside>
    {/if}
{/block}
