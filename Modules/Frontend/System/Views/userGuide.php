<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Guide</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/docsify/lib/themes/vue.css">
    <!-- Prism.js theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css">

    <style>
        .video-wrapper {
            width: 100%;
            max-width: 560px;
            margin: 20px auto;
            aspect-ratio: 16 / 9;
            position: relative;
        }

        .video-wrapper iframe {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            border: none;
        }
    </style>

</head>

<body>
    <div id="app">Loading Docs...</div>
    <script>
        window.$docsify = {
            name: '📚  User Guide',
            loadSidebar: true,
            subMaxLevel: 2,
            search: {
                maxAge: 0, //0 to disable for live update during development, 86400000 cache for 1 day, this cache is for the search index
                paths: 'auto', // or explicitly list files: ['/README.md', '/userModule.md', ...]
                placeholder: 'Search...',
                noData: 'No results!',
                depth: 4
            },
            basePath: '/userGuide/view/' // 👈 tell Docsify to fetch MDs via CI4
        }
    </script>
    <script src="//cdn.jsdelivr.net/npm/docsify/lib/docsify.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/docsify/lib/plugins/search.min.js"></script>
    <!-- Prism.js languages -->
    <script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-php.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-sql.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-javascript.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/prismjs/components/prism-json.min.js"></script>

</body>

</html>