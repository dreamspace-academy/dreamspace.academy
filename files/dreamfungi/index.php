<script>
  setTimeout(redirect, 2000);
  var redirected = false;

  function redirect() {
    if (!redirected) {
      redirected = true;
      location.href = 'https://opencollective.com/dreamfungi';
    }
  }
  (function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function() {
      (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
      m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
  ga('create', 'UA-168585905-2', 'auto');
  ga('send', 'event', 'opencollective', 'redirect', {
    hitCallback: redirect
  });
</script>
