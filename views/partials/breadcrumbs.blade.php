@if($content['id'] != evo()->getConfig('site_start'))
	@php($crumbs = \EvolutionCMS\Main\Helper::breadcrumbs())
	<nav class="breadcrumbs" aria-label="breadcrumb">
		@foreach($crumbs as $crumb)
			<a href="{{ $crumb->fullLink }}">{{ $crumb->pagetitle }}</a>
			<span class="breadcrumbs__sep">/</span>
		@endforeach
		<span class="breadcrumbs__current">{{ $content['pagetitle'] }}</span>
	</nav>
	<style>
		.breadcrumbs { margin-bottom: 1.5em; font-size: 0.85em; opacity: 0.75; }
		.breadcrumbs a { text-decoration: none; }
		.breadcrumbs a:hover { text-decoration: underline; }
		.breadcrumbs__sep { margin: 0 0.4em; }
		.breadcrumbs__current { font-weight: 600; }
	</style>
@endif
