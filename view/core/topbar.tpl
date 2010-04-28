<!-- begin topbar.tpl -->
<div id="topbar">

	<h1><a href="/" title="{$_home}">{$__app_name}</a></h1>

	{if $user}
	<div id="user_welcome">
		{$_user_welcome}, <b>{$user->username}</b> / <a href="/user/logout">{$_user_logout}</a>
	</div>
	{/if}

	<p id="you_are_here">{$_you_are_here} &raquo;
	{foreach from=$you_are_here item=x}
	{$s} {$x}
	{assign var='s' value='/'}
	{/foreach}
	</p>

</div>
<!-- end topbar.tpl -->
