<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="<?php echo(SettingsManager::get_instance()->get("site_keywords"));?>">

    <title><?php echo($page["title"]);?></title>

    <link href=".<?php echo(YAONGWIKI_THEME_INCLUDE_DIR);?>/css/bootstrap.min.css" rel="stylesheet">
    <link href=".<?php echo(YAONGWIKI_THEME_INCLUDE_DIR);?>/css/default.css" rel="stylesheet">
  </head>
  <body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand" href="./"><?php echo(SettingsManager::get_instance()->get("site_title"));?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="./?recent">Recent Edits <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?create">Create</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?read&random=true">Visit Random</a>
          </li>
          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdownLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            User
          </a>
          <div class="dropdown-menu" aria-labelledby="userDropdownLink">
          <?php if (UserManager::get_instance()->authorized()) { ?>
            <a class="dropdown-item" href="./?dashboard">Dashboard</a>
            <a class="dropdown-item" href="./?profile&user-name=<?php echo(UserManager::get_instance()->get("name"));?>">My profile</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="./?signout">Sign out</a>
          <?php } else { ?>
            <a class="dropdown-item" href="./?signin">Sign in</a>
            <a class="dropdown-item" href="./?signup">Sign up</a>
          <?php } ?>
          </div> 
        </li>
        </ul>
        <form action="./" method="get" class="form-inline my-2 my-lg-0">
          <input name="search" type="hidden" value="">
          <input name="q" class="form-control mr-sm-2" type="text" placeholder="Keyword" value="<?php echo(HttpVarsManager::get_instance()->get("q"));?>" required>
          <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav>