<?php

function initializeSupabase() {
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$url = $_ENV['SUPABASE_URL'];
$reference_id = preg_replace('|https?://(.+?)\.supabase\.co|', '$1', $url);

$supabase = new Supabase\CreateClient(
    $_ENV['SUPABASE_KEY'],    
    $reference_id
);

return $supabase;
}

function parseQuery($query){
    if (is_object($query) && method_exists($query, 'getBody')) {
      $body = json_decode((string)$query->getBody(), true);
      $data = $body['data'][0] ?? null;
  } else {
      $data = $query->data[0] ?? null;
  }

  return $data;
}

function parseQueryArray($query){
    if (is_object($query) && method_exists($query, 'getBody')) {
      $body = json_decode((string)$query->getBody(), true);
      $data = $body['data'] ?? null;
  } else {
      $data = $query->data ?? null;
  }

  return $data;
}

function formatTime($timestamp){
    $date = new DateTime($timestamp);
    $date->setTimezone(new DateTimeZone('America/Los_Angeles'));
    return $date->format('M j, Y g:i A T');
}
?>