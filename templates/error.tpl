{extends file="layouts/base.tpl"}

{block name="content"}
    <h1>{$statusCode} {$publicMessage|escape}</h1>

    {if $debug}
        <details open>
            <summary>Exception details</summary>
            <p>{$exception.class|escape}: {$exception.message|escape}</p>
            <p>{$exception.file|escape}:{$exception.line}</p>
            <pre>{$exception.trace|escape}</pre>
        </details>
    {/if}
{/block}
