<?php 
    include 'helper.php';
    include 'model.php';
    include 'connection.php';


    function getUser($id) {
        $pdo = (object)['pdo' => DBConnection::getDB()];
        $user = Model::findOne($pdo, array('id' => $id), 'users');

        return $user;
    }

    function getDashboardDetails($id) {
        $pdo = (object)['pdo' => DBConnection::getDB()];

        $transactions =  Model::find($pdo, array('user_id' => $id), 'transactions');
        $investments = Model::find($pdo, array('id' => $id), 'investments');

        return array('transactions' => $transactions, 'investments' => $investments);
    }

    function getNextOfKin() {
        $id = $_SESSION['userInfo']['id'];
        $pdo = (object)['pdo' => DBConnection::getDB()];
        $nextOfKin = Model::findOne($pdo, array('user_id' => $id), 'next_of_kin');

        return $nextOfKin;
    }

    function getAccountDetails() {
        $id = $_SESSION['userInfo']['id'];
        $pdo = (object)['pdo' => DBConnection::getDB()];
        $bankDetails = Model::findOne($pdo, array('user_id' => $id), 'bank_details');

        return $bankDetails;
    }

    function updateUserDetails($isNextOfKin=false) {
        // var_dump($_SESSION['userInfo']); exit;
        $id = $_SESSION['userInfo']['id'];
        $pdo = (object)['pdo' => DBConnection::getDB()];
        $errorMessages = array();

        $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : null;
        $lastName = isset($_POST['firstName']) ? $_POST['lastName'] : null;
        $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : null;
        if ($isNextOfKin) {
            $email = isset($_POST['email']) ? $_POST['email'] : null;
            $isValidEmail = Helper::isValidEmail($email);
        }

        $isValidFirstName = Helper::isValidName($firstName);
        $isValidLastName = Helper::isValidName($lastName);


        if (!$isValidLastName['isValid']) {
            $errorMessages['lastName'] = $isValidLastName['message'];
        }

        if (!$isValidFirstName['isValid']) {
            $errorMessages['firstName'] = $isValidFirstName['message'];
        }

        if (!is_numeric($phoneNumber)) {
            $errorMessages['phoneNumber'] = 'Phone number is invalid';
        }

        if ($isNextOfKin) {
            if (!$isValidEmail['isValid']) {
                $errorMessages['email'] = $isValidEmail['message'];
            }
        }

        if (sizeof($errorMessages) != 0) {
            Helper::jsonResponse(array('success' => false, 'message' => $errorMessages));
        }

        $userDetails = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber
        );

        if ($isNextOfKin) {
            $userDetails['email'] = $email;
            $nextOfKin = Model::findOne($pdo, array('user_id' => $id), 'next_of_kin');

            if (!$nextOfKin) {
                if (Model::create($pdo, $userDetails, 'next_of_kin')) {
                    $_SESSION['nextOfKin'] = $userDetails;
                    Helper::jsonResponse(array('success' => true, 'message' => 'User details updated successfully'));
                }
            } else {
                if(Model::update($pdo, $userDetails, array('user_id' => $id), 'next_of_kin')) {
                    // var_dump($userDetails); exit;
                    $_SESSION['nextOfKin'] = $userDetails;
                    Helper::jsonResponse(array('success' => true, 'message' => 'User details updated successfully'));
                }
            }
            Helper::jsonResponse(array('success' => false, 'message' => 'Server error'));
        }

        if(Model::update($pdo, $userDetails, array('id' => $id), 'users')) {
            $_SESSION['userInfo']['first_name'] = $userDetails['first_name'];
            $_SESSION['userInfo']['last_name'] = $userDetails['last_name'];
            $_SESSION['userInfo']['phone_number'] = $userDetails['phone_number'];
            Helper::jsonResponse(array('success' => true, 'message' => 'User details updated successfully'));
        }

        Helper::jsonResponse(array('success' => false, 'message' => 'Server error'));
    }

    function updateBankAccountDetails($id) {
        $pdo = (object)['pdo' => DBConnection::getDB()];
        $errorMessages = array();

        $accountNumber = isset($_POST['accountNumber']) ? $_POST['accountNumber'] : null;
        $accountName = isset($_POST['accountName']) ? $_POST['accountName'] : null;
        $bankName = isset($_POST['bankName']) ? $_POST['bankName'] : null;
        $bankCode = isset($_POST['bankCode']) ? $_POST['bankCode'] : '111';
        
        $isValidAccountName = Helper::isValidName($accountName);
        $isValidBankName = Helper::isValidName($bankName);

        if (!$isValidBankName['isValid']) {
            $errorMessages['bankName'] = $isValidBankName['message'];
        }

        if (!$isValidAccountName['isValid']) {
            $errorMessages['accountName'] = $isValidAccountName['message'];
        }

        if (!is_numeric($bankCode)) {
            $errorMessages['codoe'] = 'Code is invalid';
        }

        if (!is_numeric($accountNumber)) {
            $errorMessages['accountNumber'] = 'Account number is invalid';
        }

        if (sizeof($errorMessages) != 0) {
            Helper::jsonResponse(array('success' => false, 'message' => $errorMessages));
        }

        $userBank = $user = Model::findOne($pdo, array('user_id' => $id), 'bank_details');
        $bankDetails = array(
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'bank_name' => $bankName,
            'bank_code' => $bankCode
        );

      //  var_dump($bankDetails); exit;

        if (!$userBank) {
            $bankDetails['user_id'] = $id;
            if (Model::create($pdo, $bankDetails, 'bank_details')) {
                $_SESSION['accountDetails'] = $bankDetails;
                Helper::jsonResponse(array('success' => true, 'message' => 'User bank details updated successfully'));
            }
        } else {
            if(Model::update($pdo, $bankDetails, array('user_id' => $id), 'bank_details')) {
                $_SESSION['accountDetails']= $bankDetails;
                Helper::jsonResponse(array('success' => true, 'message' => 'User bank details updated successfully'));
            }
        }

        Helper::jsonResponse(array('success' => false, 'message' => 'Server error'));
    }

?>