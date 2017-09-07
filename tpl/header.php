<!doctype html>

<html>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# bravoyourcity: http://ogp.me/ns/fb/bravoyourcity#">
<meta property="fb:app_id" content="<?=FB_APPID?>" /> 

<?php if (isset($ogMetadata)): ?>
   <?php foreach($ogMetadata as $property => $content): ?>
<meta <?= strpos($property, 'og:') === 0 ? 'property' : 'name' ?>="<?= $property ?>"   content="<?= htmlspecialchars($content) ?>" /> 
   <?php endforeach ?>
<?php else:?>
<meta property="og:url"    content="<?=G_URL?>" />
<meta property="og:title"  content="bravo your city!" /> 
<meta property="og:description"  content="BRAVO YOUR CITY! publishes authentic and unique stories for the world -- by the world -- about the world. BYC is a user-generated, global travel and social enrichment magazine platform with written and visual content that is submitted by everyday people but then edited and published professionally by our BYC editorial team. BYC's sweet and simple submission parameters of: 1 location, 1-5 photos and 1-500 words make it easy to submit multiple stories." /> 
<meta property="og:image"  content="<?=G_URL?>/img/fb-logo.png" /> 
<?php endif?>

<title><?= isset($ogMetadata) && isset($ogMetadata['og:title']) ? $ogMetadata['og:title'] : 'bravo your city!' ?></title>
<link href="/img/favicon.ico" rel="icon" type="image/x-icon" />
<?php if (defined('CANONICAL_URL')): ?>
<link rel="canonical" href="<?= CANONICAL_URL ?>" />
<?php endif ?>

<?php require_once(dirname(__FILE__) . '/../css/style.php'); ?>
<?php require_once(dirname(__FILE__) . '/../jst/script.php'); ?>

<script type="text/javascript">

var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?= GA_ACCOUNT ?>']);
<?php if (defined('GA_DOMAINNAME')): ?>
_gaq.push(['_setDomainName', '<?= GA_DOMAINNAME ?>']);
<?php endif ?>
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>



<script type="text/javascript" src="//use.typekit.net/hfm6ymy.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

<?php if (isset($_SESSION['alert']) && $_SESSION['alert']): ?>
<script type="text/javascript">
$(window).on('load', function() {
  if (loader) {
    loader.create(<?= json_encode($_SESSION['alert']) ?>);
  }
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif ?>
</head>

<body class="<?= isset($__body_class) ? $__body_class : '' ?>">

<a class="topOfPage" name="topOfPage"></a>

<div class="header <?= isset($_SESSION['user']) ? 'loggedin' : 'loggedout' ?>">

  <ul class="header-menu-1">

    <li id="header-icon-pair-left" class="loggedin-hidden"><a class="header-facebook-icon" title="Find us on Facebook" href="http://www.facebook.com/bravoyourcity"></a></li>
    <li id="header-icon-pair-right" class="loggedin-hidden"><a class="header-twitter-icon" title="Follow us on Twitter" href="http://www.twitter.com/bycmicroguides"></a></li>
    <li class="user-sign-in loggedin-hidden"><a class="major-nav">Sign In</a></li>
    
    <?php if (isset($_SESSION['user'])):?>
    <?php $messages = message::getDisplayRegionMessages(message::DISPLAY_REGION_LEFT_OF_GREETING); ?>
    <?php if (count($messages)): ?>
    <li class="message">
      <span class="arrow"></span>
      <?php foreach ($messages as $message): ?>
      <?= $message['text'] ?> <a href="/message/hide/<?= $message['_id'] ?>?returnUrl=<?= G_URL ?>share"><span class="close"></span></a><img height=1 width=1 src="<?= G_URL ?>message/logView/<?= $message['_id'] ?>"/>
    <?php endforeach ?>
    </li>
    <?php endif ?>
    <li class="user-loggedin header-hovermenu-parent mobile-layout-hidden"><span class="author">Bravo, <a data-slug="<?= $_SESSION['user']['slug'] ?>"><?=$_SESSION['user']['username']?></a>!</span><ul class="header-hovermenu">
        <li class="arrow"></li>
        <?php if (user::isAdmin()): ?>
        <li class="user_admin"><a class="major-subnav" href="/index/status/pending">Pending</a></li>
        <li class="user_admin"><a class="major-subnav" href="/index/status/rejected">Rejected</a></li>
        <li class="user_admin"><a class="major-subnav" href="/admin/users">Users</a></li>
        <li class="user_admin"><a class="major-subnav" href="/admin/dynamicConfig">Config</a></li>
        <li class="user_admin"><a class="major-subnav" href="/admin/microguides">Microguides</a></li>
        <li class="user_admin"><a class="major-subnav" href="/admin/merryGoRound">Merry Go Round</a></li>
        <?php endif?>
        <li><a class="major-subnav" href="/share/bio">Edit Bio</a></li>
        <li><a class="major-subnav" href="/my/account">Account Settings</a></li>
        <li><a class="major-subnav" href="/user/logout?ajax=0">Sign Out</a></li>
      </ul>
    </li>
    <?php endif?>

  </ul>

  <div class="header-menu-3-wrapper">
          <ul class="header-menu-3">
            <li class="mobile-layout-visible"><a class="major-nav" href="/share/">Share Your City!</a></li>
            <li class="mobile-layout-visible"><a class="major-nav" href="/microguide/">Microguides</a></li>
            <li class="mobile-layout-hidden loggedin-hidden"><a class="major-nav" href="/static/how-it-works">How it Works</a></li>
            <li class="mobile-layout-visible header-hovermenu-parent menu-places-search">Places<ul class="header-hovermenu">
                <li class="arrow"></li>
                <li class="" data-query='[["New York", "query"], ["NYC", "query"]]'><a class="major-subnav">NYC</a></li>
                <li class="" data-query='["Paris", "query"]'><a class="major-subnav">Paris</a></li>
                <li class="" data-query='["Hong Kong", "query"]'><a class="major-subnav">Hong Kong</a></li>
                <li class="" data-query='["Seoul", "query"]'><a class="major-subnav">Seoul</a></li>
                <li class="" data-query='["Bay Area", "query"]'><a class="major-subnav">Bay Area</a></li>
                <li class="" data-query='["Silicon Valley", "query"]'><a class="major-subnav">Silicon Valley</a></li>
                <li class="" data-query='["Malaysia", "query"]'><a class="major-subnav">Malaysia</a></li>
                <li class="" data-query='["Japan", "query"]'><a class="major-subnav">Japan</a></li>
                <li class="" data-query='["Budapest", "query"]'><a class="major-subnav">Budapest</a></li>
                <li class="" data-query='["Ireland", "query"]'><a class="major-subnav">Ireland</a></li>
              </ul>
            </li>
            <?php if (isset($_SESSION['user'])):?>
            <li class="header-hovermenu-parent mobile-layout-hidden">Create<ul class="header-hovermenu">
                <li class="arrow"></li>
                <li><a class="major-subnav" href="/share/">New Story</a></li>
                <li><a class="major-subnav" href="/microguide/create">New Microguide</a></li>
              </ul>
            </li>
            <li class="header-hovermenu-parent mobile-layout-hidden">My Stuff<ul class="header-hovermenu">
                <li class="arrow"></li>
                <li><a class="major-subnav" href="/my/stories">My Stories</a></li>
                <li><a class="major-subnav" href="/my/microguides">My Microguides</a></li>
              </ul>
            </li>
            <?php endif ?>
            <li class="menu-about"><span class="loggedin-hidden">About Us</span><span class="loggedout-hidden about-us-icon"></span><ul>
                <li class="arrow"></li>
                <li class=""><a class="major-subnav" href="/static/faq">FAQ</a></li>
                <li class=""><a class="major-subnav" href="/static/about">Info</a></li>
                <li class=""><a class="major-subnav" href="/static/fifty">50%</a></li>
                <?php #<li class=""><a class="major-subnav" href="/static/team">Team</a></li> ?>
                <li class=""><a class="major-subnav" href="/static/terms">Terms</a></li>
                <li class=""><a class="major-subnav" href="/static/privacy">Privacy</a></li>
                <li class=""><a class="major-subnav" href="/static/how-it-works">How it Works</a></li>
                <li class="last"><a class="major-subnav" href="/forms/publisher">For Publishers</a></li>
              </ul>
            </li>
            <?php if (isset($_SESSION['user'])):?>
            <li class="mobile-layout-visible"><a class="major-subnav" href="/user/logout?ajax=0">Sign Out</a></li>
            <?php else: ?>
            <li class="mobile-layout-visible user-sign-in"><a class="major-nav">Sign In</a></li>
            <?php endif ?>
          </ul>
  </div>

  <div class="header-split">

    <div class="header-split-left">

      <div class="header-logo">
        <a class="header-logo-a" href="<?=G_URL?>"><img src="/img/byc-logo-5.png" /></a>
      </div>

    </div>

    <div class="header-split-right">

      <ul class="header-midline-navigation">
        <li>
          <a class="major-nav" href="/share/">
            <div class="main-text">Share</div>
            <div class="sub-text">Your City</div>
          </a>
        </li>
        <li class="menu-places menu-places-search">
          <div class="main-text">Explore</div>
          <div class="sub-text">The World</div><ul>
              <li class="arrow"></li>
              <li class="" data-query='[["New York", "query"], ["NYC", "query"]]'><a class="major-subnav">NYC</a></li>
              <li class="" data-query='["Paris", "query"]'><a class="major-subnav">Paris</a></li>
              <li class="" data-query='["Hong Kong", "query"]'><a class="major-subnav">Hong Kong</a></li>
              <li class="" data-query='["Seoul", "query"]'><a class="major-subnav">Seoul</a></li>
              <li class="" data-query='["Bay Area", "query"]'><a class="major-subnav">Bay Area</a></li>
              <li class="" data-query='["Silicon Valley", "query"]'><a class="major-subnav">Silicon Valley</a></li>
              <li class="" data-query='["Malaysia", "query"]'><a class="major-subnav">Malaysia</a></li>
              <li class="" data-query='["Japan", "query"]'><a class="major-subnav">Japan</a></li>
              <li class="" data-query='["Budapest", "query"]'><a class="major-subnav">Budapest</a></li>
              <li class="" data-query='["Ireland", "query"]'><a class="major-subnav">Ireland</a></li>
            </ul>
        </li>
        <li>
          <a class="major-nav" href="/microguide/">
            <div class="main-text">Browse</div>
            <div class="sub-text">Microguides</div>
          </a>
        </li>
      </ul>

      <?php require_once '_search.php';?>

    </div>

  </div><!-- header-split -->

  <hr class="header-border-midline" />
  <hr class="header-border-bottom" />

</div><!-- header -->

<div class="clear"></div>

