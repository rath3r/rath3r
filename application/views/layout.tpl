<!DOCTYPE html>
<htmt lang="en">
<head>
    <meta charset="{$smarty.const.APP_ENCODING|lower}">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <title>{$title|regex_replace:'/-/':'â€”'}</title>
    <meta content="Thomas Meehan" name="author">
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" name="viewport">
    <meta name="keywords" content="{$smarty.const.SITE_META_KEYWORDS}">
    <meta name="description" content="{$smarty.const.SITE_META_DESCRIPTION}">
{foreach from=$css_files item=stylesheet}
    <link href="{$stylesheet}" media="all" rel="stylesheet">
{/foreach}
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="author" href="/humans.txt" type="text/plain">
{include file="fragments/js_head.tpl"}
</head>
<body class="{$bodyClass}">
{include file='fragments/header.tpl'}
{block name=head}{/block}
{block name=content}{/block}
{include file="fragments/footer.tpl"}
{include file='fragments/js_plugins.tpl'}
{foreach from=$js_files item=js_file}
<script src="{$js_file}"></script>
{/foreach}
{include file='fragments/analytics.tpl'}
</body>
</html>
