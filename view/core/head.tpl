<?xml encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="creation-date" content="{$__today_rfc2822}" />
<meta name="description" content="" />
<meta name="keywords" content="" />

{foreach from=$css_list item=css}
<link rel="stylesheet" href="{$__web_theme}/{$css[0]}" type="text/css" media="{$css[1]}" />
{/foreach}

<!-- JQuery -->
<script language="javascript" type="text/javascript" src="{$__web_common}/js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="{$__web_common}/js/jquery.cookie.js"></script>
<script language="javascript" type="text/javascript" src="{$__web_common}/js/ui.core.js"></script>
<script language="javascript" type="text/javascript" src="{$__web_common}/js/ui.datepicker.js"></script>
<link type="text/css" href="{$__web_common}/js/ui.datepicker.css" rel="stylesheet" />
<!-- /JQuery -->

<script language="javascript" type="text/javascript" src="{$__web_common}/js/language/{$__app_lang}.js"></script>
{foreach from=$js_list item=js}
<script language="javascript" type="text/javascript" src="{$__web_common}/js/{$js}"></script>
{/foreach}

<title>{$__app_name} :: {$page_title}</title>
</head>

<body>
