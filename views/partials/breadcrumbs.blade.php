@if($content['id'] != evo()->getConfig('site_start'))
	@php($crumbs = \EvolutionCMS\Main\Helper::breadcrumbs())
	<nav class="breadcrumbs" aria-label="breadcrumb">
		@foreach($crumbs as $crumb)
			<a href="{{ $crumb->fullLink }}">{{ $crumb->pagetitle }}</a>
			<span class="breadcrumbs__sep">/</span>
		@endforeach
		<span class="breadcrumbs__current">{{ $content['pagetitle'] }}</span>
	</nav>
@endif
