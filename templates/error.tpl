{extends file="layouts/base.tpl"}

{block name="content"}
    <section class="error-page">
        <p class="error-page__code">{$statusCode}</p>
        <h1>{$publicMessage|escape}</h1>
        <p>Не удалось открыть эту страницу. Попробуйте вернуться на главную.</p>
        <a class="button" href="/">На главную</a>

        {if $debug}
            <details class="debug-details" open>
                <summary>Exception details</summary>
                <p>{$exception.class|escape}: {$exception.message|escape}</p>
                <p>{$exception.file|escape}:{$exception.line}</p>
                <pre>{$exception.trace|escape}</pre>
            </details>
        {/if}
    </section>
{/block}
