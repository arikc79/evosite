@extends('layout')

@section('bodyClass', 'is-preload')

@section('content')
	<header>
		<h2>{{ $content['pagetitle'] }}</h2>
		<p>{{ $content['description'] }}</p>
	</header>
	@foreach($articles as $doc)
		@include('partials.article-item', ['item' => [
			'pagetitle' => $doc->pagetitle,
			'description' => $doc->description,
			'introtext' => $doc->introtext,
			'link' => $doc->fullLink,
			'createdon' => date('d.m.Y', $doc->createdon_orig),
			'thumb' => \EvolutionCMS\Main\Helper::articleThumb($doc->id),
		]])
	@endforeach
@endsection

@section('aside')
	@include('partials.sidebar')
@endsection
