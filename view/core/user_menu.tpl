<!-- begin menu.tpl -->

<!--{*
@author      Leonardo Vidarte <lvidarte@gmail.com>
@version     $Id: menu.tpl 1 2009-03-11 11:29:08Z xleo $
@license     http://www.gnu.org/licenses/gpl.html GNU General Public License
@package     view
@subpackage  user
*}-->

<div id="user-logout">
	{$_user_username}&nbsp;&raquo;&nbsp;
	{if $user}
	<b><a href="/user/data">{$user->username}</a></b> [<a href="/user/logout">{$_user_logout}</a>]
	{else}
	[<a href="/user/login{$from}">{$_user_login}</a>]
	{/if}
</div>

<h1>Playground</h1>
<!-- end menu.tpl -->
