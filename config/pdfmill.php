<?php

return [

    /*
     * Your deployment of pdfmill, e.g. https://pdfmill.your-subdomain.workers.dev
     */
    'url' => env('PDFMILL_URL'),

    /*
     * The API_KEY secret of that deployment.
     */
    'key' => env('PDFMILL_KEY'),

    /*
     * Seconds to wait for a response. Merges of large files and custom fonts can take a while.
     */
    'timeout' => (int) env('PDFMILL_TIMEOUT', 120),

    'connect_timeout' => (int) env('PDFMILL_CONNECT_TIMEOUT', 10),

];
