<?php 
  global $lms_layout_title;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../plugins/adminlte-3.2.0/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

  <title><?php _e($lms_layout_title??''); ?> - Library Mgmt System</title>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        
        <div class="show dropdown">
          <a class="btn btn-secondary-outline dropdown-toggle" href="#" role="button" id="dropdownMenuLink2" data-toggle="dropdown">
            <span><i class="fas fa-globe-americas"></i> <?php echo esc_html((defined('LMS_CURRENT_LOCALE'))?LMS_CURRENT_LOCALE:'English (Default)'); ?></span>
          </a>

          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink2" style="min-width: 1rem;">
            <?php 
              $availableLocales = lms_get_available_locales();
            ?>
            <a class="dropdown-item" href="" onclick="changeLang('default');return false;">
              English (Default)
            </a>
            <?php foreach($availableLocales as $locale): ?>
            <a class="dropdown-item" href="" onclick="changeLang('<?php echo addslashes($locale); ?>');return false;">
              <?php echo LMS_LOCALES_NAME[$locale]??$locale; ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link text-center">
      <!-- <img loading="lazy" src="images/schlogo/<?php echo $_SESSION['SchoolLogo']; ?>" alt="<?php echo $_SESSION['SchoolNameShortC']; ?>" class="brand-image img-circle elevation-3"
           style="opacity: .8"> -->
      <span class="brand-text font-weight-light">
        <span class="d-block"><?php _e('Library Mgmt System'); ?></span>
        <span class="d-block small"><?php _e('Librarian Portal'); ?></span>
      </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel my-1  text-center">
        <!-- <div class="image">
          <img loading="lazy" src="/assets/php/getimage.php?methumb=<?php echo $_SESSION['UserID']; ?>" class="img-circle elevation-2" alt="User Image">
        </div> -->
        <div class="info">
          <a href="#" class="d-block my-0">
            <span><?php printf(__('Welcome, %s.'), esc_html($_SESSION['LibrarianName'])); ?></span>
          </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link nav-link-btn">
                  <i class="fas fa-home nav-icon"></i>
                    <p>
                      <span><?php _e('Home'); ?></span>
                    </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link">
                  <i class="fas fa-exchange-alt nav-icon"></i>
                    <p>
                      <span><?php _e('Circulation'); ?></span>
                    </p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item ml-3">
                    <a href="circ-checkout.php" class="nav-link nav-link-btn">
                      <i class="fas fa-sign-out-alt nav-icon"></i>
                      <p><?php _e('Check Out Item'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="circ-checkin.php" class="nav-link nav-link-btn">
                      <i class="fas fa-sign-in-alt nav-icon"></i>
                      <p><?php _e('Check In Item'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="circ-renew.php" class="nav-link nav-link-btn">
                      <i class="fas fa-sync-alt nav-icon"></i>
                      <p><?php _e('Renew Item'); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link">
                  <i class="fas fa-tags nav-icon"></i>
                    <p>
                      <span><?php _e('Cataloguing'); ?></span>
                    </p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item ml-3">
                    <a href="biblio-search.php" class="nav-link nav-link-btn">
                      <i class="fas fa-search nav-icon"></i>
                      <p><?php _e('Search'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="biblio-add.php" class="nav-link nav-link-btn">
                      <i class="fas fa-plus nav-icon"></i>
                      <p><?php _e('Add New'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="biblio-bulkupload.php" class="nav-link nav-link-btn">
                      <i class="fas fa-file-import nav-icon"></i>
                      <p><?php _e('Bulk Upload'); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link">
                  <i class="fas fa-book-reader nav-icon"></i>
                    <p>
                      <span><?php _e('Patrons'); ?></span>
                    </p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item ml-3">
                    <a href="patron-search.php" class="nav-link nav-link-btn">
                      <i class="fas fa-search nav-icon"></i>
                      <p><?php _e('Search'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="patron-add.php" class="nav-link nav-link-btn">
                      <i class="fas fa-plus nav-icon"></i>
                      <p><?php _e('Add New'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="patron-bulkupload.php" class="nav-link nav-link-btn">
                      <i class="fas fa-file-import nav-icon"></i>
                      <p><?php _e('Bulk Upload'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="patron-category-view.php" class="nav-link nav-link-btn">
                      <i class="fas fa-user-tag nav-icon"></i>
                      <p><?php _e('Manage Categories'); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link">
                  <i class="far fa-clipboard nav-icon"></i>
                    <p>
                      <span><?php _e('Reporting'); ?></span>
                    </p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item ml-3">
                    <a href="reporting-overdue.php" class="nav-link nav-link-btn">
                      <i class="far fa-clock nav-icon"></i>
                      <p><?php _e('Overdue Books'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="reporting-mostcheckedoutbooks.php" class="nav-link nav-link-btn">
                      <i class="fas fa-book nav-icon"></i>
                      <p><?php _e('Most C/o Books'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="reporting-mostcheckoutspatron.php" class="nav-link nav-link-btn">
                      <i class="fas fa-book-reader nav-icon"></i>
                      <p><?php _e('Most C/o Patron'); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./" class="nav-link">
                  <i class="fas fa-cogs nav-icon"></i>
                    <p>
                      <span><?php _e('Administration'); ?></span>
                    </p>
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item ml-3">
                    <a href="librarian-view.php" class="nav-link nav-link-btn">
                      <i class="fas fa-user-tie nav-icon"></i>
                      <p><?php _e('Librarian Mgmt'); ?></p>
                    </a>
                  </li>
                  <li class="nav-item ml-3">
                    <a href="settings.php" class="nav-link nav-link-btn">
                      <i class="fas fa-sliders-h nav-icon"></i>
                      <p><?php _e('Settings'); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="changepassword.php" class="nav-link nav-link-btn">
                  <i class="fas fa-unlock-alt nav-icon"></i>
                    <p>
                      <span><?php _e('Change Password'); ?></span>
                    </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="login.php?do=logout" class="nav-link nav-link-btn">
                  <i class="fas fa-sign-out-alt nav-icon"></i>
                    <p>
                      <span><?php _e('Logout'); ?></span>
                    </p>
                </a>
              </li>
            </ul>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  
</div>


<div class="content-wrapper">