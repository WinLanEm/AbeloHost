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
                    {include
                        file="partials/article-card.tpl"
                        article=$article
                        headingLevel=3
                        showMeta=true
                    }
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p class="empty-state">Статьи пока не опубликованы.</p>
    {/foreach}
{/block}
