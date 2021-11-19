<style>body {padding: 40px; font-family: Roboto, Arial}</style>
<h3>snpnz Project</h3>
<li><a href="/docs/">Private API</a>
<li><a href="/migrate/">Migrate basic</a>
<li><a href="/migrate/?data">Migrate test data</a>
<pre>
<?php
print_r(array(
    "HTTP_AUTHORIZATION" => $_SERVER['HTTP_AUTHORIZATION'],
    "snpnz-auth" => $_COOKIE["snpnz-auth"]
));
?>
</pre>
<pre>
<?php
require_once('./_includes/keeper.php');
?>
</pre>
