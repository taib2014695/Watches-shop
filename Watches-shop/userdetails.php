<?php
require_once './data/dbconnect.php';
include './ultility/userultilities.php';
include './ultility/orderultilities.php';
session_start();

function getCountProducts() {
    if (isset($_SESSION['GuestCarts'])) {
        return count($_SESSION['GuestCarts']);
    } else {
        return 0;
    }
}

/// Get user's infors
$admin = '';
$account = '';
if (isset($_SESSION['userId'])) 
{
    $admin = getUserById($con, $_SESSION['userId']);
    
    if (isset($_GET['id']) && intval($admin['Admin']) === 1) {
        $account = getUserById($con, $_GET['id']);
    }
    else {
        header("Location: 404.php");
    }
} 
else {
    header("Location: login.php");
}

/// Update user's infos
$resultUpdate = NULL;
if (isset($_SESSION['userId'])) {
    if (isset($_POST['txtAddress'])) {
        $accountId = $account['Id'];
        $accountSex = $_POST['txtSex'];
        $accountDoB = $_POST['txtDoB'];
        $accountIdCard = $_POST['txtIdCard'];
        $accountAddress = $_POST['txtAddress'];
        $accountPhone = $_POST['txtPhone'];

        $sql = "UPDATE `users` SET `Sex`='$accountSex', "
                . "`DoB`='$accountDoB', `IdCard`='$accountIdCard', "
                . "`Address`='$accountAddress',`Phone`='$accountPhone' "
                . "WHERE Id='$accountId'";
        $resultUpdate = mysqli_query($con, $sql);
        
        if ($resultUpdate) {
            $account = getUserById($con, $accountId);
        }
    }
}

?>
<!doctype html>
<html>
    <!-- Head HTML -->
    <head>
        <meta charset="utf-8">
        <title>Thông tin tài khoản</title>
        <link href="styles/site.css" rel="stylesheet" type="text/css">
        <link href="styles/usercontrolGuest.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="javascript.js"></script>
    </head>

    <!-- Body HTML -->
    <body>
        <div class="divContainer">
            <!-- Header -->
            <?php include './Templates/header.php'; ?>

            <!-- Top Menu -->
            <?php include './Templates/topmenu.php'; ?>

            <!-- Body Wrapper Second Level -->
            <div class="divWrapper_2" style="overflow: hidden">
                <!-- Body Wrapper First Level -->
                <div class="divWrapper_1">
                    <!-- Left Menu -->
                    <?php include './Templates/leftmenu.php'; ?>

                    <!-- Main -->
                    <div class="divMain">
                        <article class="articleContent">
                            <!-- User's infos -->
                            <p class="pPageTitle">Thông tin tài khoản</p>
                            <section>
                                <form id="formUpdateUserInfors" action="" method="post">

                                    <table>
                                        <tr>
                                            <td width="160px">Email: </td>
                                            <td><?php echo $account['Email']; ?></td>
                                        </tr> 
                                        <tr>
                                            <td>Họ tên: </td>
                                            <td><?php echo $account['Name']; ?></td>
                                        </tr> 
                                        <tr>
                                            <td>Giới tính: </td>
                                            <td>
                                                <input type="radio" name="txtSex" value="0" <?php if (intval($account['Sex'])===0) {echo "checked=''";} ?>/> Nam &nbsp;
                                                <input type="radio" name="txtSex" value="1" <?php if (intval($account['Sex'])!=0) {echo "checked=''";} ?>/> Nữ 
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td>Ngày sinh: </td>
                                            <td>
                                                <input type="date" name="txtDoB" value="<?php echo $account['DoB']; ?>" required="" />
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td>Chứng minh thư: </td>
                                            <td>
                                                <input type="text" name="txtIdCard" value="<?php echo $account['IdCard']; ?>" size="30" required="" />
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td>Địa chỉ: </td>
                                            <td><input type="text" name="txtAddress" value="<?php echo $account['Address']; ?>" size="30" required="" /></td>
                                        </tr> 
                                        <tr>
                                            <td>Số điện thoại: </td>
                                            <td><input type="text" name="txtPhone" value="<?php echo $account['Phone']; ?>" size="30" required="" /></td>
                                        </tr> 
                                        <tr>
                                            <td></td>
                                            <td><input type="submit" name="btnSubmit" value="Cập nhật" /> <input type="reset" name="btnReset" value="Nhập lại" /></td>
                                        </tr>
                                    </table>
                                </form>

                                <?php
                                if (isset($_SESSION['userId'])) {
                                    if (isset($_POST['txtAddress'])) {
                                        if ($resultUpdate) {
                                ?>
                                    <p class="pResultLogin">Cập nhập thông tin thành công.</p>
                                <?php
                                        } else {
                                ?>
                                    <p class="pResultLogin">Cập nhập thông tin thất bại.</p>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </section>

                            <!-- User's orders -->
                            <p class="pPageTitle">Quản lý đơn hàng</p>
                            <section>
                                <table class="tableCart" id="tableCart" border="1" cellpadding="5" cellspacing="0">
                                    <!-- Top Table -->
                                    <tr id="trTop_TableCart">
                                        <th width="60px">Mã số</th>
                                        <th width="110px">Thời gian đặt</th>
                                        <th width="160px">Khách hàng</th>
                                        <th width="140px">Tổng cộng</th>
                                        <th width="150px">Trạng thái</th>
                                    </tr>
                                    <!-- Orders List -->
                                    <?php
                                    /// Get user's orders

                                        $sql = "SELECT * FROM `orders` WHERE UserId = ".$account['Id']." ORDER BY Id DESC";
                                        $result = mysqli_query($con, $sql);
                                        if (mysqli_num_rows($result) >= 1) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $order = getOrderById($con, $row['Id']);
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="aContent" href="orderdetails.php?id=<?php echo $order['Id']; ?>"><?php echo $order['Id']; ?></a>
                                        </td>
                                        <td>
                                            <?php echo $order['CreateTime']; ?>
                                        </td>
                                        <td>
                                            <?php echo $order['Name']; ?>
                                        </td>
                                        <td>
                                            <span class="spanProductPrice">
                                            <?php echo getOrderTotal($con, $order['Id'])." VNĐ"; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo getOrderStatus($con, $order['Status']); ?>
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        }
                    
                                    ?>
                                </table>
                            </section>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include './Templates/footer.php'; ?>
        </div>
    </body>
</html>
