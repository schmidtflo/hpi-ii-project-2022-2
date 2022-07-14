<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>

    <script>
        window.articles = {{ Js::from($articles) }};
        window.hrbs = {{ Js::from($hrbs) }};
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {

                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .vis-item.article {
                @apply bg-blue-300 text-sm;
            }
            .vis-item.hrb {
                @apply bg-red-300 text-sm;
            }
        }
    </style>
</head>
<body>
{{ $company->name }}

<div id="timeline"></div>

<ul>
@foreach($articles as $article)
    <li>{{ $article['title'] }}</li>
@endforeach
</ul>
</body>
<script src="/js/app.js"></script>

</html>
