{if $smarty.const.SITE_GOOGLE_ANALYTICS && $smarty.server['HTTP_HOST'] != 'dev.rath3r.com'}
<script type="text/javascript">{literal}
var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '{/literal}{$smarty.const.SITE_GOOGLE_ANALYTICS}{literal}']);
	_gaq.push(['_setDomainName', 'rath3r.com']);
	_gaq.push(['_setCampNameKey', 'utm_name']);
	_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
{/literal}</script>
{/if}
