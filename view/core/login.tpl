<!-- begin login.tpl -->

<!--{*
@author      Leonardo Vidarte <lvidarte@gmail.com>
@version     $Id: login.tpl 66 2009-06-16 14:17:12Z xleo $
@license     http://www.gnu.org/licenses/gpl.html GNU General Public License
@package     view
@subpackage  templates
@subpackage  user
*}-->

<h3>{$page_title}</h3>

<br />
<form class="login" action="/user/login" method="post" name="formu">

	{if $error}
	<p class="error">{$error}</p>
	{/if}

	{if $message}
	<p class="info">{$message}</p>
	{/if}

	<table class="form">
		<tr>
			<th><label for="username">{$_user_username}</label></th>
			<td><input type="text" id="username" name="username" size="16"/></td>
		</tr>
		<tr>
			<th><label for="password">{$_user_password}</label></th>
			<td><input type="password" id="password" name="password" size="16"/></td>
		</tr>
		<tr>
			<td class="noborder"></td>
			<td>
				<input class="nodisplay" type="hidden" name="__from__" value="{$from}"/>
				<input class="nodisplay" type="hidden" name="__login__" value="__login__"/>
				<input class="buttom" type="submit" name="login" value="{$_user_login}"/>
			</td>
		</tr>
	</table>

</form>

<!-- end login.tpl -->
