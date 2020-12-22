<?php
  session_start();
 include 'controller.php';
  function getMatureDate($dateCreated, $amount) {
    $months = 0;

    if ($amount < 60000) {
        $months = 1;
    } else if ($amount >= 60000 && $amount < 600000) {
        $months = 3;
    } else if ($amount >= 600000 && $amount < 1500000) {
        $months = 6;
    } else if ($amount >= 1500000) {
        $months = 12;
    }
    $maturedDate = date('Y-m-d', strtotime("+" . $months ." months", strtotime($dateCreated)));

    return $maturedDate;
}

function getPercent($amount) {
  if ($amount < 60000) {
      return 0.1;
  } else if ($amount >= 60000 && $amount < 600000) {
      return 0.15;
  } else if ($amount >= 600000 && $amount < 1500000) {
      return 0.2;
  } else if ($amount >= 1500000) {
      return 0.3;
  }
}

function getTotalAmountInvested($investments) {
  $totalAmount = 0;

  foreach($investments as $investment) {
    $totalAmount+=$investment['amount'];
  }

  return $totalAmount;

}

function getTotalAmountWithdrawn($transactions) {
  $totalAmount = 0;

  foreach($transactions as $transaction) {
    if ($transaction['transaction_type'] == 0) {
      $totalAmount+=$transaction['amount'];
    }
  }

  return $totalAmount;
}



function getBalance($transactions, $investments) {
  $amountWithdrawn = getTotalAmountWithdrawn($transactions);

  $maturedInvestments = 0;
  foreach($investments as $investment) {
    if (date('d-m-y h:i:s') > getMatureDate($investment['date_created'], $investment['amount'])) {
      $maturedInvestmentse+=$investment['amount'] + ($investment['amount'] * getPercent($investment['amount']));
    }
  }

  return $maturedInvestments - $amountWithdrawn;
}

  $subPath = $_SERVER['HTTP_HOST'] == 'localhost:8888' ? '/investment-dashboard' : '';
  $currentPage = 'Dashboard';
  $header = 'Last ten Transaction';

  if ($subPath == '/investment-dashboard') {
    $user = getUser(1);
    $_SESSION['userInfo'] = $user;

    if (!isset($_SESSION['nextOfKin'])) {
      $nextOfKin = getNextOfKin();
      $_SESSION['nextOfKin'] = $nextOfKin;
    }

    if (!isset($_SESSION['accountDetails'])) {
      $bankDetails = getAccountDetails();
      $_SESSION['accountDetails'] = $bankDetails;
    }

  } else {

    if (!isset($_SESSION['userInfo'])) {
      $tokenCode = $_GET['code'];
      $id = $_GET['_id'];

      if (!isset($_GET['_id']) || !isset($_GET['code'])) {
        header("Location: http://wiseinvestment.com.ng/?showloginmodal=1");
      }
  
      if (!is_numeric($id) || !is_numeric($tokenCode)) {
        die('Server Error CODE: 997223546');
      }
  
      $user = getUser($id);
  
      if (!$user || $user['code_token'] != $tokenCode) {
        die('Server Error CODE: 997223547');
      }
  
      $_SESSION['userInfo'] = $user;
      $nextOfKin = getNextOfKin();
      $_SESSION['nextOfKin'] = $nextOfKin;
      $bankDetails = getAccountDetails();
      $_SESSION['accountDetails'] = $bankDetails;
    }

  }

  $url = explode('?', $_SERVER['REQUEST_URI'])[0];
  $details = getDashboardDetails($_SESSION['userInfo']['id']);

  if ($url == '/transactions') {
    // echo 'Fuck you'; exit;
    $currentPage = 'Transactions';
    $header = 'All Transactions';
    include 'transactions.php';
    exit;
  } else if ($url == '/investments') {
    $currentPage = 'Investments';
    $header = 'All Investments';
    include 'investments.php';
    exit;
  } else if ($url == '/profile') {
    $currentPage = 'Profile';
    $header = 'My Profile';
    include 'profile.php';
    exit;
  } else if ($url == '/updateUserDetails') {
    updateUserDetails();
  } else if ($url == '/updateNextKinDetails') {
    updateUserDetails(true);
  } else if ($url == '/updateBankAccountDetails') {
    updateBankAccountDetails($_SESSION['userInfo']['id']);
  } else if ($url == '/withdraw') {
    $header = 'Withdraw Funds';
    include 'withdraw.php';
    exit;
  } else if ($url == '/withdrawFunds') {
    withdrawFunds();
  } else if ($url == '/logout') {
    include 'logout.php';
  } else if ($url == '/') {
    // pass 
  } else {
    header("Location: /");
  }

  $totalAmountInvested = 0;
  for ($i = 0; $i < sizeof($details['investments']); $i++) {
      $totalAmountInvested+=$details['investments'][$i]['amount'];
  }

  $transactions = $details['transactions'];
?>

<!doctype html>
<html lang="en">
<?php include 'head.php'; ?>

<body class="sidebar-menu-collapsed">
  <div class="se-pre-con"></div>
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

    <!-- chatting -->

    <!-- //chatting -->

    <!-- accordions -->
    <!-- //accordions -->

    <!-- modals -->
      <?php include 'transaction.php'; ?>
    <!-- //modals -->

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
</body>

</html>
  