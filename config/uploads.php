<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Hero slider uploads
  |--------------------------------------------------------------------------
  | Laravel validation "max" for images is in kilobytes.
  | PHP post_max_size / upload_max_filesize must be >= these limits (see php.ini).
  */
  'hero_image_max_kb' => (int) env('HERO_IMAGE_MAX_KB', 16384), // 16 MB per file
  'hero_per_file_mb' => (int) env('HERO_PER_FILE_MB', 16),
  'hero_post_max_mb' => (int) env('HERO_POST_MAX_MB', 64),
  'hero_max_files' => (int) env('HERO_MAX_FILES', 20),
];
