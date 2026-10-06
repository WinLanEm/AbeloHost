{extends file="layouts/base.tpl"}

{block name="content"}
    <h1>{$pageTitle|escape}</h1>

    {foreach $categories as $category}
        <section>
            <header>
                <h2>{$category.name|escape}</h2>
                <p>{$category.description|escape}</p>
            </header>

            <div>
                {foreach $category.articles as $article}
                    <article>
                        <a href="/articles/{$article.id}">
                            <img
                                src="{$article.imagePath|escape}"
                                alt=""
                                width="480"
                                height="270"
                            >
                            <h3>{$article.title|escape}</h3>
                        </a>
                        <p>{$article.description|escape}</p>
                        <p>
                            <time datetime="{$article.publishedAt|escape}">
                                {$article.publishedAtLabel|escape}
                            </time>
                            · {$article.viewCount} просмотров
                        </p>
                    </article>
                {/foreach}
            </div>

            <p><a href="/categories/{$category.id}">Все статьи</a></p>
        </section>
    {foreachelse}
        <p>Статьи пока не опубликованы.</p>
    {/foreach}
{/block}
