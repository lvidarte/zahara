
<!-- begin error404.tpl -->

<!--
@author      Leonardo Vidarte <lvidarte@gmail.com>
@version     $Id: error404.tpl 62 2009-06-16 04:09:30Z xleo $
@license     http://www.gnu.org/licenses/gpl.html GNU General Public License
@package     view
@subpackage  templates
@subpackage  http
-->

<h3>{$___404_message}</h3>

<br />

{if $___404_description}
<div class="error">{$___404_description}</div>
{/if}

{if $code}
<p>{printf 0=$_errorCode 1=$code}</p>
{/if}

{if $dump}
<h4>{$_errorDump}</h4>
{$dump}
{/if}

{if $backtrace}
<h4>{$_errorBackTrace}</h4>
<pre class="wrap"><code>{$backtrace}</code></pre>
{/if}

<!-- end error404.tpl -->
