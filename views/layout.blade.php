<!DOCTYPE HTML>
<html>
<head>
	<title>{{ $content['pagetitle'] }} - My Evolution Blog</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="[(site_url)]assets/site/assets/css/main.css" />
	<link rel="stylesheet" href="[(site_url)]assets/site/assets/css/slider.css" />
</head>
<body class="@yield('bodyClass', 'is-preload')">
	<div id="wrapper">
		@include('partials.header')
		@include('partials.menu')
		<div id="main">
			@yield('content')
		</div>
		@yield('aside')
	</div>
	<script src="[(site_url)]assets/site/assets/js/jquery.min.js"></script>
	<script src="[(site_url)]assets/site/assets/js/browser.min.js"></script>
	<script src="[(site_url)]assets/site/assets/js/breakpoints.min.js"></script>
	<script src="[(site_url)]assets/site/assets/js/util.js"></script>
	<script src="[(site_url)]assets/site/assets/js/main.js"></script>
	<script src="[(site_url)]assets/site/assets/js/slider.js"></script>
</body>
</html>
