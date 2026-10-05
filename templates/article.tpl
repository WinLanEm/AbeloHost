{extends file="layouts/base.tpl"}

{block name="content"}
    <article>
        <h1>{$pageTitle|escape}</h1>
        <p>Это статическая страница статьи с идентификатором {$articleId|escape}.</p>
        <p><a href="/">Вернуться на главную</a></p>
    </article>
{/block}
