<script>
  setTimeout(redirect, 2000);
  var redirected = false;

  function redirect() {
    if (!redirected) {
      redirected = true;
      location.href = 'https://opencollective.com/dreamfungi';
    }
  }
</script>
