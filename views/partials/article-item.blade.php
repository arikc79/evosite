<article class="post">
	<header>
		<div class="title">
			<h2><a href="{{ $item['link'] }}">{{ $item['pagetitle'] }}</a></h2>
			<p>{{ $item['description'] }}</p>
		</div>
		<div class="meta">
			<time class="published">{{ $item['createdon'] }}</time>
		</div>
	</header>
	{!! $item['thumb'] !!}
	<p>{{ $item['introtext'] }}</p>
	<footer>
		<ul class="actions">
			<li><a href="{{ $item['link'] }}" class="button large">@lang('read_more')</a></li>
		</ul>
	</footer>
</article>
