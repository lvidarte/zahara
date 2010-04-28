<!-- begin playground.tpl -->

<!--
@author      Leonardo Vidarte <lvidarte@gmail.com>
@version     $Id: playground.tpl 1 2009-03-11 11:29:08Z xleo $>
@license     http://www.gnu.org/licenses/gpl.html GNU General Public License
@package     view
@subpackage  templates
@subpackage  misc
-->

{include file="core/head.tpl"}

{include file="core/user_menu.tpl"}

<div class="playgroundMenu">{$playgroundIndexLink}&nbsp;&raquo;&nbsp;{$playgroundClassLinks}</div>

{$__CONTENT__}

{include file="core/foot.tpl"}

<!-- end playground.tpl -->
