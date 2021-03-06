<?php
// var_dump($_SESSION); exit;
?>

<!doctype html>
<html lang="en">
<?php include 'head.php'; ?>

<body class="sidebar-menu-collapsed">
  <div class="se-pre-con"></div>
  <style>
            .error-message {
                font-size: 13px;
                color: red;
            }
            .success-message {
                display: none;
            }
        </style>
<section>
  <!-- sidebar menu start -->
   <?php include 'sidebar.php'; ?>
  <!-- //sidebar menu end -->

  <!-- header-starts -->
    <?php include 'header.php'; ?>
  <!-- //header-ends -->

  <!-- main content start -->
<div class="main-content">

  <!-- content -->
  <div class="container-fluid content-top-gap">
    <!-- statistics data -->
      <?php include 'stat.php'; ?>
    <!-- //statistics data -->

    <!-- profile details -->
    <div class="card card_border py-2 mb-4">
                <div class="cards__heading">
                    <h3>Withdraw Funds <span></span></h3>
                </div>
                <div class="card-body">
                <div id="m-success" class="alert alert-success success-message" role="alert">
                        
                    </div>
                    <div id="m-failure" class="alert alert-danger success-message" role="alert">
                        
                    </div>
                    <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4" class="input__label">Amount</label>
                                <input type="number" class="form-control input-style" id="withdraw" placeholder="Amount" required>
                            </div>
                        </div>
                        <button id="withdraw-funds" type="submit" class="btn btn-primary btn-style mt-4">Submit</button>
                    
                </div>
            </div>

    <!-- // end of profile -->

  </div>
  <!-- //content -->
</div>
<!-- main content end-->
</section>
  <!--footer section start-->
  <?php include 'footer.php'; ?>
<!--footer section end-->
<!-- move top -->
<button onclick="topFunction()" id="movetop" class="bg-primary" title="Go to top">
  <span class="fa fa-angle-up"></span>
</button>
<?php include 'script.php'; ?>
<script src="<?=$subPath . '/assets/js/w.js'?>"></script>
</body>

</html>
  