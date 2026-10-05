<?php

return [

    /*
     * Your deployment of pdf-lib-workers, e.g. https://pdf-lib-workers.your-subdomain.workers.dev
     */
    'url' => env('PDF_LIB_WORKERS_URL'),

    /*
     * The API_KEY secret of that deployment.
     */
    'key' => env('PDF_LIB_WORKERS_KEY'),

    /*
     * Seconds to wait for a response. Merges of large files and custom fonts can take a while.
     */
    'timeout' => (int) env('PDF_LIB_WORKERS_TIMEOUT', 120),

    'connect_timeout' => (int) env('PDF_LIB_WORKERS_CONNECT_TIMEOUT', 10),

];
